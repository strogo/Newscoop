/******************************************************************************

CAMPSITE is a Unicode-enabled multilingual web content
management system for news publications.
CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
Copyright (C)2000,2001  Media Development Loan Fund
contact: contact@campware.org - http://www.campware.org
Campware encourages further development. Please let us know.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

******************************************************************************/

/******************************************************************************

CParser methods implementation

******************************************************************************/

#include <stdio.h>
#include <string.h>
#include <list>
#include <unistd.h>
#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <typeinfo>

#include "auto_ptr.h"
#include "actions.h"
#include "parser.h"
#include "util.h"
#include "error.h"
#include "data_types.h"

#define ROOT_STATEMENTS ST_PUBLICATION ", " ST_ISSUE ", " ST_SECTION ", "\
ST_ARTICLE ", " ST_LIST ", " ST_INCLUDE ", " ST_IF ", " ST_URLPARAMETERS ", "\
ST_FORMPARAMETERS ", " ST_PRINT ", " ST_DATE ", " ST_LOCAL ", "\
ST_SUBSCRIPTION ", " ST_EDIT ", " ST_SELECT ", " ST_USER ", " ST_SEARCH

#define LISSUE_STATEMENTS ST_LIST ", " ST_SECTION ", " ST_ARTICLE ", "\
ST_URLPARAMETERS ", " ST_FORMPARAMETERS ", " ST_PRINT ST_DATE ", "\
ST_INCLUDE ", " ST_IF ", " ST_LOCAL ", " ST_SUBSCRIPTION ", " ST_EDIT\
", " ST_SELECT ", " ST_USER ", " ST_SEARCH

#define LSECTION_STATEMENTS ST_LIST ", " ST_ARTICLE ", " ST_URLPARAMETERS\
", " ST_FORMPARAMETERS ", " ST_PRINT ST_DATE ", " ST_INCLUDE ", " ST_IF\
"," ST_LOCAL ", " ST_SUBSCRIPTION ", " ST_EDIT ", " ST_SELECT ", " ST_USER\
", " ST_SEARCH

#define LARTICLE_STATEMENTS ST_URLPARAMETERS ", " ST_FORMPARAMETERS ", "\
ST_PRINT ", " ST_DATE ", " ST_INCLUDE ", " ST_IF ", " ST_LOCAL ", "\
ST_SUBSCRIPTION ", " ST_EDIT ", " ST_SELECT ", " ST_USER ", " ST_SEARCH

#define LIST_STATEMENTS ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", "\
ST_SEARCHRESULT

#define LV_ROOT 1
#define LV_LISSUE 2
#define LV_LSECTION 4
#define LV_LARTICLE 8
#define LV_LSUBTITLE 16

#define SUBLV_NONE 0
#define SUBLV_IFPREV 1
#define SUBLV_IFNEXT 2
#define SUBLV_IFLIST 4
#define SUBLV_IFISSUE 8
#define SUBLV_IFSECTION 16
#define SUBLV_IFARTICLE 32
#define SUBLV_EMPTYLIST 64
#define SUBLV_IFALLOWED 128
#define SUBLV_SUBSCRIPTION 256
#define SUBLV_USER 512
#define SUBLV_LOGIN 1024
#define SUBLV_LOCAL 2048
#define SUBLV_IFPUBLICATION 4096
#define SUBLV_SEARCH 8192
#define SUBLV_SEARCHRESULT 16384
#define SUBLV_WITH 32768
#define SUBLV_IFLANGUAGE 65536


// macro definition

#define ONE_OF_LV(cl, gl) (cl & (gl))

#define CheckForLevel(lv, lvs, line, column)\
{\
if (!ONE_OF_LV(lv, lvs)) {\
SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,\
LvStatements(lv), line, column);\
WaitForStatementEnd(false);\
return 1;\
}\
}

#define CheckNotLevel(lv, lvs, line, column)\
{\
if (ONE_OF_LV(lv, lvs)) {\
SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,\
LvStatements(lv), line, column);\
WaitForStatementEnd(false);\
return 1;\
}\
}

#define CheckForStatement(l, req, r, c)\
{\
if (l->res() != CMS_LEX_STATEMENT) {\
SetPError(parse_err, PERR_STATEMENT_MISSING, MODE_PARSE, req, r, c);\
WaitForStatementEnd(false);\
return 0;\
}\
}

#define CheckForIdentifier(l, req, r, c)\
{\
if (l->res() != CMS_LEX_IDENTIFIER) {\
SetPError(parse_err, PERR_IDENTIFIER_MISSING, MODE_PARSE, req, r, c);\
WaitForStatementEnd(false);\
return 1;\
}\
}

#define CheckForAtom(l)\
{\
if (l->atom() == 0)\
FatalPError(parse_err, PERR_NO_ATOM_IN_LEXEM, MODE_PARSE, "",\
lex.prevLine(), lex.prevColumn());\
}

#define CheckForAtomType(a, tn, er, req, line, column)\
{\
if (dynamic_cast<tn>(a) == NULL)\
FatalPError(parse_err, er, MODE_PARSE, req, line, column);\
}

#define CheckForText(al, l)\
{\
if (l->textStart() && l->textLen() > 0)\
al.insert(al.end(), new CActText(l->textStart(), l->textLen()));\
}

#define CheckForEOF(l, err)\
{\
if (l->res() == CMS_ERR_EOF) {\
if (err > 0)\
SetPError(parse_err, err, MODE_PARSE, "", lex.prevLine(), lex.prevColumn());\
return -1;\
}\
}

#define ValidateOperator(lexem, attr)\
{\
if (!attr->validOperator(lexem->atom()->identifier()))\
throw InvalidOperator();\
}

#define ValidateDType(l, attr)\
{\
if (!attr->validValue(l->atom()->identifier()))\
throw InvalidValue();\
}

#define CheckForEndSt(l)\
{\
if (l->res() == CMS_LEX_END_STATEMENT) {\
SetPError(parse_err, PERR_IDENTIFIER_MISSING, MODE_PARSE, "",\
lex.prevLine(), lex.prevColumn());\
}\
}

#define RequireAtom(l)\
{\
l = lex.getLexem();\
DEBUGLexem("req atom", l);\
CheckForEOF(l, PERR_EOS_MISSING);\
CheckForEndSt(l);\
CheckForAtom(l);\
}

// end macro definition

#ifdef _DEBUG

class CDebugHandler
{
public:
	CDebugHandler(const string& p_rcoMethod, const string& p_rcoArg)
		: m_rcoMethod(p_rcoMethod), m_rcoArg(p_rcoArg)
	{
		cout << pthread_self() << ": " << m_rcoMethod << " - begin: " << m_rcoArg << endl;
	}

	~CDebugHandler()
	{
		cout << pthread_self() << ": " << m_rcoMethod << " - end: " << m_rcoArg << endl;
	}

private:
	string m_rcoMethod;
	string m_rcoArg;
};

#define FUNC_DEBUG(func, arg) CDebugHandler dh(func, arg);

#else

#define FUNC_DEBUG(func, arg)

#endif


// CErrorList implementation

void CErrorList::clear()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete *coIt;
		*coIt = NULL;
		erase(coIt);
	}
}

const CErrorList& CErrorList::operator =(const CErrorList& o)
{
	if (this == &o)
		return *this;
	clear();
	for (const_iterator coIt = o.begin(); coIt != o.end(); ++coIt)
		push_back(new CError(**coIt));
	return *this;
}


// CParserMap implementation

CParser* CParserMap::find(const string& p_rcoTpl) const
{
	const_iterator coIt = map<string, CParser*>::find(p_rcoTpl);
	if (coIt == end())
		return NULL;
	return (*coIt).second;
}

bool CParserMap::Insert(CParser* p_pcoParser)
{
	if (find(p_pcoParser->getTpl()))
		return false;
	(*this)[p_pcoParser->getTpl()] = p_pcoParser;
	return true;
}

void CParserMap::Erase(CParser* p_pcoParser)
{
	erase(p_pcoParser->getTpl());
}

void CParserMap::Reset() const
{
	for (const_iterator coIt = begin(); coIt != end(); ++coIt)
		(*coIt).second->reset();
}

void CParserMap::Clear()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
		erase(coIt);
	}
}


// CParser implementation

CParserMap CParser::m_coPMap;
CMutex CParser::m_coMapMutex;
TK_MYSQL CParser::s_MYSQL(NULL);

// MapTpl: map template file to m_pchTplBuf buffer
void CParser::MapTpl() throw (ExStat)
{
	if (tpl == "")
		return ;
	struct stat last_tpl_stat;
	try
	{
		int res;
		if (m_nTplFD >= 0)
			res = fstat(m_nTplFD, &last_tpl_stat);
		else
			res = stat(tpl.c_str(), &last_tpl_stat);
		if (res != 0)
			throw ExStat(EMAP_STAT);
		if (!S_ISREG(last_tpl_stat.st_mode))
			throw ExStat(EMAP_NOTREGFILE);
		if (tpl_stat.st_mtime != last_tpl_stat.st_mtime
		    || tpl_stat.st_ctime != last_tpl_stat.st_ctime
		    || m_pchTplBuf == NULL)
		{
			if (parsed || m_pchTplBuf == NULL)
			{
				CRWMutexHandler h2(&m_coOpMutex, true);
				parsed = false;
				UnMapTpl();
				if ((m_nTplFD = open(tpl.c_str(), O_RDONLY)) < 0)
					throw ExStat(EMAP_EOPENFILE);
				m_pchTplBuf = (const char*)mmap(0, last_tpl_stat.st_size, PROT_READ, MAP_SHARED, m_nTplFD, 0);
				close(m_nTplFD);
				m_nTplFD = -1;
				if (m_pchTplBuf == MAP_FAILED || m_pchTplBuf == NULL)
					throw ExStat(EMAP_FAILED);
				m_nTplFileLen = last_tpl_stat.st_size;
				lex.reset(m_pchTplBuf, m_nTplFileLen);
			}
		}
		tpl_stat = last_tpl_stat;
	}
	catch (ExStat& rcoEx)
	{
		tpl_stat = last_tpl_stat;
		throw rcoEx;
	}
}

// UnMapTpl: unmap template file
inline void CParser::UnMapTpl() throw()
{
	lex.reset(NULL, 0);
	if (m_pchTplBuf != NULL)
	{
		munmap((void*)m_pchTplBuf, m_nTplFileLen);
		m_pchTplBuf = NULL;
		m_nTplFileLen = 0;
	}
	if (m_nTplFD >= 0)
	{
		close(m_nTplFD);
		m_nTplFD = -1;
	}
}

// LvStatements: return string containig statement names allowed in a given level:
// root, list issue, list section, list article, list subtitle
// Parameters:
//		int level - level: LV_ROOT, LV_LISSUE, LV_LSECTION, LV_LARTICLE, LV_LSUBTITLE
const char* CParser::LvStatements(int level)
{
	if (level & LV_LARTICLE || level & LV_LSUBTITLE)
		return LARTICLE_STATEMENTS;
	else if (level & LV_LSECTION)
		return LSECTION_STATEMENTS;
	else if (level & LV_LISSUE)
		return LISSUE_STATEMENTS;
	else if (level & LV_ROOT)
		return ROOT_STATEMENTS;
	else
		return "";
}

// LvListSt: return string containig list type statements allowed in a given level:
// root, list issue, list section, list article, list subtitle
// Parameters:
//		int level - level: LV_ROOT, LV_LISSUE, LV_LSECTION, LV_LARTICLE, LV_LSUBTITLE
const char* CParser::LvListSt(int level)
{
	if (level & LV_LARTICLE || level & LV_LSUBTITLE)
		return ST_SEARCH;
	else if (level & LV_LSECTION)
		return ST_ARTICLE ", " ST_SEARCH;
	else if (level & LV_LISSUE)
		return ST_SECTION ", " ST_ARTICLE ", " ST_SEARCH;
	else if (level & LV_ROOT)
		return ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", " ST_SEARCH;
	else
		return "";
}

// IfStatements: return string containing if type statements allowed in a given level
// (root, list issue, list section, list article, list subtitle)
// and sublevel (user, login, search, with etc.)
// Parameters:
//		int level - level: LV_ROOT, LV_LISSUE, LV_LSECTION, LV_LARTICLE, LV_LSUBTITLE
//		int sublevel - sublevel: SUBLV_NONE, SUBLV_IFPREV, SUBLV_IFNEXT, SUBLV_IFLIST,
//			SUBLV_IFISSUE, SUBLV_IFSECTION, SUBLV_IFARTICLE, SUBLV_EMPTYLIST,
//			SUBLV_IFALLOWED, SUBLV_SUBSCRIPTION, SUBLV_USER, SUBLV_LOGIN, SUBLV_LOCAL
//			SUBLV_IFPUBLICATION, SUBLV_SEARCH, SUBLV_SEARCHRESULT, SUBLV_WITH
string CParser::IfStatements(int level, int sublevel)
{
	string s_str;
	if (level == LV_ROOT || sublevel & SUBLV_EMPTYLIST)
		s_str = ST_PUBLICATION ", " ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", " ST_ALLOWED;
	else if (sublevel & SUBLV_IFNEXT || sublevel & SUBLV_IFPREV)
		s_str = ST_LIST ", " ST_PUBLICATION ", " ST_ISSUE ", " ST_SECTION ", "
		        ST_ARTICLE ", " ST_ALLOWED;
	else
		s_str = ST_PREVIOUSITEMS ", " ST_NEXTITEMS ", " ST_LIST ", " ST_PUBLICATION
		        ", " ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", " ST_ALLOWED;
	s_str += ", " ST_IF " " ST_PUBLICATION "|" ST_ISSUE "|" ST_SECTION
	         "|" ST_ARTICLE", " ST_SUBSCRIPTION;
	if ((sublevel & SUBLV_USER) == 0)
		s_str += ", " ST_USER;
	if ((sublevel & SUBLV_LOGIN) == 0)
		s_str += ", " ST_LOGIN;
	if ((sublevel & SUBLV_SEARCH) == 0)
		s_str += ", " ST_SEARCH;
	if ((sublevel & SUBLV_WITH) == 0)
		s_str += ", " ST_SUBTITLE;
	if (level & LV_LSUBTITLE && sublevel & SUBLV_WITH)
	{
		s_str += ", " ST_CURRENTSUBTITLE;
	}
	return s_str;
}

// PrintStatements: return string containig print type statements allowed in a given level
// (root, list issue, list section, list article, list subtitle)
// and sublevel (user, login, search, with etc.)
// Parameters:
//		int level
//		int sublevel
string CParser::PrintStatements(int level, int sublevel)
{
	string s_str;
	s_str = ST_PUBLICATION ", " ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", " ST_IMAGE ", "
	        ST_SEARCH ", " ST_SUBSCRIPTION ", " ST_USER;
	if (level > LV_ROOT || sublevel & SUBLV_SEARCHRESULT)
		s_str += ", " ST_LIST;
	if ((sublevel & SUBLV_LOGIN) == 0)
		s_str += ", " ST_LOGIN;
	if ((sublevel & SUBLV_SEARCH) == 0)
		s_str += ", " ST_SEARCH;
	if (level & LV_LSUBTITLE)
		s_str += ", " ST_SUBTITLE;
	return s_str;
}

// EditStatements: return string containig edit type statements allowed in a given
// sublevel (user, login, search, with etc.)
// Parameters:
//		int sublevel
string CParser::EditStatements(int sublevel)
{
	string s_str;
	if (sublevel & SUBLV_SUBSCRIPTION)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_SUBSCRIPTION;
	if (sublevel & SUBLV_USER)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_USER;
	if ((sublevel & SUBLV_LOGIN) == 0)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_LOGIN;
	if ((sublevel & SUBLV_SEARCH) == 0)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_SEARCH;
	return s_str;
}

// SelectStatements: return string containig select type statements allowed in a given
// sublevel (user, login, search, with etc.)
// Parameters:
//		int sublevel
string CParser::SelectStatements(int sublevel)
{
	string s_str;
	if (sublevel & SUBLV_SUBSCRIPTION)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_SUBSCRIPTION;
	if (sublevel & SUBLV_USER)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_USER;
	if ((sublevel & SUBLV_SEARCH) == 0)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_SEARCH;
	return s_str;
}

// DEBUGLexem: print lexem debug information
void CParser::DEBUGLexem(const char* c, const CLexem* l)
{
	if (debug() == true)
	{
		cout << "<!-- @LEXEM " << c << ": " << (int)l->res();
		if (l->atom())
			cout << " atom: " << l->atom()->identifier() << ", " << l->atom()->atomType();
		if (l->textStart())
		{
			cout << " text %";
			cout.write(l->textStart(), l->textLen());
			cout << "% len: " << l->textLen();
		}
		cout << " -->\n";
	}
}

// WaitForStatementStart: read from input file until it finds a start statement
// Parameters:
//		CActionList& al - reference to list of actions
const CLexem* CParser::WaitForStatementStart(CActionList& al)
{
	l = lex.getLexem();
	DEBUGLexem("wf start 1", l);
	while (l->res() != CMS_LEX_START_STATEMENT
	        && l->res() != CMS_ERR_EOF)
	{
		CheckForText(al, l);
		if (l->res() < 0)
			return l;
		l = lex.getLexem();
		DEBUGLexem("wf start 2", l);
	}
	CheckForText(al, l);
	return l;
}

// WaitForStatementEnd: read from input file until it finds an end statement
const CLexem* CParser::WaitForStatementEnd(bool write_errors)
{
	const CLexem *c_lexem = lex.getLexem();
	DEBUGLexem("wf end 1", c_lexem);
	while (c_lexem->res() != CMS_LEX_END_STATEMENT
	        && c_lexem->res() != CMS_ERR_EOF)
	{
		if (write_errors == true)
			SetPError(parse_err, PERR_END_STATEMENT_MISSING, MODE_PARSE, CLex::endToken(),
			          lex.prevLine(), lex.prevColumn());
		c_lexem = lex.getLexem();
		DEBUGLexem("wf end 2", c_lexem);
	}
	if (c_lexem->res() == CMS_ERR_EOF)
		SetPError(parse_err, PERR_END_STATEMENT_MISSING, MODE_PARSE, CLex::endToken(),
		          lex.prevLine(), lex.prevColumn());
	return c_lexem;
}

// LMod2Level: return level corresponding to given list modifier
inline int CParser::LMod2Level(int lm)
{
	if (lm == CMS_ST_ISSUE)
		return LV_LISSUE;
	else if (lm == CMS_ST_SECTION)
		return LV_LSECTION;
	else if (lm == CMS_ST_SUBTITLE)
		return LV_LSUBTITLE;
	else if (lm == CMS_ST_ARTICLE)
		return LV_LARTICLE;
	return 0;
}

// ValidDateForm: return true if given string is a valid date format
int CParser::ValidDateForm(const char* df)
{
	if (df == NULL)
		return 1;
	static char valid_chars[] = "MWYymcdejD%";
	while (*df)
	{
		if (*df == '%' && strchr(valid_chars, *(++df)) == 0)
			return 0;
		if (*df)
			df++;
	}
	return 1;
}

// SetWriteErrors: set the parse_err_printed and write_err_printed members
// for this parser instance and for included templates
void CParser::SetWriteErrors(bool p_bWriteErrors)
{
	FUNC_DEBUG("CParser::SetWriteErrors", tpl);
	CRWMutexHandler h(&m_coOpMutex, true);
	parse_err_printed = !p_bWriteErrors;
	write_err_printed = !p_bWriteErrors;
	for (StringSet::iterator sh_i = child_tpl.begin(); sh_i != child_tpl.end(); ++sh_i)
	{
		if (*sh_i == tpl)
			continue;
		CParser* p = m_coPMap.find(*sh_i);
		if (p != NULL)
			p->SetWriteErrors(p_bWriteErrors);
	}
}

// HLanguage: parse language statement; add CActLanguage action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HLanguage(CActionList& al, int lv, int sublv)
{
	if ((sublv & SUBLV_LOCAL) == 0)
	{
		CheckForLevel(lv, LV_ROOT, lex.prevLine(), lex.prevColumn());
		CheckNotLevel(sublv, SUBLV_SEARCHRESULT, lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	al.insert(al.end(), new CActLanguage(l->atom()->identifier()));
	WaitForStatementEnd(true);
	return 0;
}

// HInclude: parse include statement; add CActInclude action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
inline int CParser::HInclude(CActionList& al)
{
	RequireAtom(l);
	CheckForAtomType(l->atom(), const CAtom*, PERR_NOT_VALUE, "",
	                 lex.prevLine(), lex.prevColumn());
	string itpl_name;
	if ((l->atom()->identifier())[0] == '/')
		itpl_name = document_root + l->atom()->identifier();
	else
	{
		itpl_name = tpl;
		itpl_name.erase(itpl_name.rfind('/'));
		itpl_name += string("/") + l->atom()->identifier();
	}
	if (parent_tpl.find(itpl_name) != parent_tpl.end())
		FatalPError(parse_err, PERR_INCLUDE_CICLE, MODE_PARSE, "", lex.prevLine(), lex.prevColumn());
	child_tpl.insert(itpl_name);
	CParser* p;
	if ((p = m_coPMap.find(itpl_name)) == NULL)
		p = new CParser(itpl_name);
	try
	{
		p->parent_tpl.insert(parent_tpl.begin(), parent_tpl.end());
		p->parse();
	}
	catch (ExStat& rcoEx)
	{
		return rcoEx.ErrNr();
	}
	catch (ExMutex& rcoEx)
	{
		return ERR_NOACCESS;
	}
	al.insert(al.end(), new CActInclude(itpl_name, &m_coPMap));
	WaitForStatementEnd(true);
	return 0;
}

// HPublication: parse publication statement; add CActPublication action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
inline int CParser::HPublication(CActionList& al, int level, int sublevel)
{
	if ((sublevel & SUBLV_LOCAL) == 0)
	{
		CheckForLevel(level, LV_ROOT, lex.prevLine(), lex.prevColumn());
		CheckNotLevel(sublevel, SUBLV_SEARCHRESULT, lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	attr = st->findAttr(l->atom()->identifier(), CMS_CT_DEFAULT);
	if (case_comp(l->atom()->identifier(), "off") != 0
	    && case_comp(l->atom()->identifier(), "default") != 0)
	{
		RequireAtom(l);
		ValidateDType(l, attr);
		CParameter param(attr->attribute(), "",
		                 attr->compOperation(g_coEQUAL, l->atom()->identifier()));
		al.insert(al.end(), new CActPublication(param));
	}
	else
		al.insert(al.end(), new CActPublication(CParameter(attr->attribute(), "", NULL)));
	WaitForStatementEnd(true);
	return 0;
}

// HIssue: parse include statement; add CActIssue action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
inline int CParser::HIssue(CActionList& al, int level, int sublevel)
{
	if ((sublevel & SUBLV_LOCAL) == 0)
	{
		CheckForLevel(level, LV_ROOT, lex.prevLine(), lex.prevColumn());
		CheckNotLevel(sublevel, SUBLV_SEARCHRESULT, lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	attr = st->findAttr(l->atom()->identifier(), CMS_CT_DEFAULT);
	if (case_comp(l->atom()->identifier(), "off") != 0
	    && case_comp(l->atom()->identifier(), "default") != 0
	    && case_comp(l->atom()->identifier(), "current") != 0)
	{
		RequireAtom(l);
		ValidateDType(l, attr);
		CParameter param(attr->attribute(), "",
		                 attr->compOperation(g_coEQUAL, l->atom()->identifier()));
		al.insert(al.end(), new CActIssue(param));
	}
	else
		al.insert(al.end(), new CActIssue(CParameter(attr->attribute(), "", NULL)));
	WaitForStatementEnd(true);
	return 0;
}

// HSection: parse include statement; add CActSection action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
inline int CParser::HSection(CActionList& al, int level, int sublevel)
{
	if ((sublevel & SUBLV_LOCAL) == 0)
	{
		CheckForLevel(level, LV_ROOT | LV_LISSUE, lex.prevLine(), lex.prevColumn());
		CheckNotLevel(sublevel, SUBLV_SEARCHRESULT, lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	attr = st->findAttr(l->atom()->identifier(), CMS_CT_DEFAULT);
	if (case_comp(l->atom()->identifier(), "off") != 0
	    && case_comp(l->atom()->identifier(), "default") != 0)
	{
		RequireAtom(l);
		ValidateDType(l, attr);
		CParameter param(attr->attribute(), "",
		                 attr->compOperation(g_coEQUAL, l->atom()->identifier()));
		al.insert(al.end(), new CActSection(param));
	}
	else
		al.insert(al.end(), new CActSection(CParameter(attr->attribute(), "", NULL)));
	WaitForStatementEnd(true);
	return 0;
}

// HArticle: parse include statement; add CActArticle action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
inline int CParser::HArticle(CActionList& al, int level, int sublevel)
{
	if ((sublevel & SUBLV_LOCAL) == 0)
		CheckForLevel(level, LV_ROOT | LV_LISSUE | LV_LSECTION, lex.prevLine(), lex.prevColumn());
	RequireAtom(l);
	attr = st->findAttr(l->atom()->identifier(), CMS_CT_DEFAULT);
	if (case_comp(l->atom()->identifier(), "off") != 0
	    && case_comp(l->atom()->identifier(), "default") != 0)
	{
		RequireAtom(l);
		ValidateDType(l, attr);
		CParameter param(attr->attribute(), "",
		                 attr->compOperation(g_coEQUAL, l->atom()->identifier()));
		al.insert(al.end(), new CActArticle(param));
	}
	else
		al.insert(al.end(), new CActArticle(CParameter(attr->attribute(), "", NULL)));
	WaitForStatementEnd(true);
	return 0;
}

// HURLParameters: parse include statement; add CActURLParameters action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
inline int CParser::HURLParameters(CActionList& al)
{
	l = lex.getLexem();
	DEBUGLexem("urlparam", l);
	long int img = -1;
	bool fromstart = false, allsubtitles = false;
	CLevel reset_from_list = CLV_ROOT;
	while (l->res() != CMS_LEX_END_STATEMENT)
	{
		if (!l->atom())
		{
			SetPError(parse_err, PERR_ATOM_MISSING, MODE_PARSE, "",
			          lex.prevLine(), lex.prevColumn());
			return 0;
		}
		if (case_comp(l->atom()->identifier(), "fromstart") == 0)
		{
			fromstart = true;
		}
		else if (case_comp(l->atom()->identifier(), "allsubtitles") == 0)
		{
			allsubtitles = true;
		}
		else if (case_comp(l->atom()->identifier(), "reset_issue_list") == 0)
		{
			reset_from_list = CLV_ISSUE_LIST;
		}
		else if (case_comp(l->atom()->identifier(), "reset_section_list") == 0)
		{
			reset_from_list = CLV_SECTION_LIST;
		}
		else if (case_comp(l->atom()->identifier(), "reset_article_list") == 0)
		{
			reset_from_list = CLV_ARTICLE_LIST;
		}
		else if (case_comp(l->atom()->identifier(), "reset_searchresult_list") == 0)
		{
			reset_from_list = CLV_SEARCHRESULT_LIST;
		}
		else if (case_comp(l->atom()->identifier(), "reset_subtitle_list") == 0)
		{
			reset_from_list = CLV_SUBTITLE_LIST;
		}
		else if (case_comp(l->atom()->identifier(), ST_IMAGE) == 0)
		{
			RequireAtom(l);
			try
			{
				Integer i(l->atom()->identifier());
				i.operator long int();
			}
			catch (InvalidValue& rcoEx)
			{
				SetPError(parse_err, PERR_DATA_TYPE, MODE_PARSE, "integer",
				          lex.prevLine(), lex.prevColumn());
				return 0;
			}
			img = strtol(l->atom()->identifier().c_str(), 0, 10);
		}
		else
		{
			string r_attrs = string(ST_IMAGE) + ", " + st->contextAttrs(CMS_CT_DEFAULT);
			SetPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE, r_attrs,
			          lex.prevLine(), lex.prevColumn());
			WaitForStatementEnd(false);
			return 0;
		}
		l = lex.getLexem();
		DEBUGLexem("urlparam2", l);
	}
	al.insert(al.end(), new CActURLParameters(fromstart, allsubtitles, img, reset_from_list));
	if (l->res() != CMS_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HFormParameters: parse FormParameters statement; add CActFormParameters action to
// actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
inline int CParser::HFormParameters(CActionList& al)
{
	l = lex.getLexem();
	DEBUGLexem("formparam", l);
	bool fromstart = false;
	if (l->res() != CMS_LEX_END_STATEMENT && l->atom()
	    && case_comp(l->atom()->identifier(), "fromstart") == 0)
	{
		fromstart = true;
	}
	al.insert(al.end(), new CActFormParameters(fromstart));
	if (l->res() != CMS_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HDate: parse date statement; add CActDate action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
inline int CParser::HDate(CActionList& al)
{
	OPEN_TRY
	RequireAtom(l);
	OPEN_TRY
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_DEFAULT);
	CLOSE_TRY
	CATCH(InvalidAttr)
		if (!ValidDateForm(l->atom()->identifier().c_str()))
			throw InvalidAttr(g_nFIND_NORMAL);
	END_CATCH
	al.insert(al.end(), new CActDate(l->atom()->identifier()));
	WaitForStatementEnd(true);
	return 0;
	CLOSE_TRY
	CATCH(InvalidAttr)
		string a_req = st->contextAttrs(CMS_CT_DEFAULT) + ", \"date format\"";
		FatalPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE,
		            a_req, lex.prevLine(), lex.prevColumn());
	END_CATCH
}

// HPrint: parse print statement; add CActPrint action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HPrint(CActionList& al, int lv, int sublv)
{
	RequireAtom(l);
	CheckForAtomType(l->atom(), const CStatement*, PERR_ATOM_NOT_STATEMENT,
	                 PrintStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
	st = CLex::findSt(l->atom()->identifier());
	if (!CActPrint::validModifier(st->id()))
	{
		SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE, PrintStatements(lv, sublv),
		          lex.prevLine(), lex.prevColumn());
		WaitForStatementEnd(false);
		return 0;
	}
	if (st->id() == CMS_ST_LIST
	    && (lv & (LV_LISSUE | LV_LSECTION | LV_LARTICLE | LV_LSUBTITLE)) == 0
	    && (sublv & SUBLV_SEARCHRESULT) == 0)
	{
		SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,
		          LvStatements(lv), lex.prevLine(), lex.prevColumn());
		WaitForStatementEnd(false);
		return 1;
	}
	if ((st->id() == CMS_ST_LOGIN && (sublv & SUBLV_LOGIN))
	    || (st->id() == CMS_ST_SUBTITLE && !(lv & LV_LSUBTITLE))
	    || (st->id() == CMS_ST_SEARCH && (sublv & SUBLV_SEARCH)))
	{
		FatalPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,
		            PrintStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	bool strictType = false;
	string type, format;
	if (st->findType(l->atom()->identifier()))
	{
		type = l->atom()->identifier();
		strictType = true;
		RequireAtom(l);
	}
	SafeAutoPtr<CPairAttrType> attrType(NULL);
	if (type != "")
		attrType.reset(st->findTypeAttr(l->atom()->identifier(), type, CMS_CT_PRINT));
	else
		attrType.reset(st->findAnyAttr(l->atom()->identifier(), CMS_CT_PRINT));
	if (attrType->second != NULL)
		type = attrType->second->name();
	const string& attrIdentifier = attrType->first->identifier();
	const string& attrAttribute = attrType->first->attribute();
	if (attrType->first->dataType() == CMS_DT_DATE)
	{
		l = lex.getLexem();
		DEBUGLexem("print", l);
		CheckForEOF(l, PERR_EOS_MISSING);
		if (l->res() != CMS_LEX_END_STATEMENT)
		{
			CheckForAtom(l);
			if (ValidDateForm(l->atom()->identifier().c_str()))
				format = l->atom()->identifier();
			else
			{
				SetPError(parse_err, PERR_INVALID_DATE_FORM, MODE_PARSE,
				          "", lex.prevLine(), lex.prevColumn());
				WaitForStatementEnd(true);
				return 0;
			}
		}
	}
	if (case_comp(attrIdentifier, "mon_name") == 0)
		format = "%M";
	if (case_comp(attrIdentifier, "wday_name") == 0)
		format = "%W";
	al.insert(al.end(), new CActPrint(attrAttribute, st->id(), type, strictType, format));
	if (l->res() != CMS_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HList: parse list statement; add CActList action to actions list (al)
// All statements between List and EndList statements are parsed, added as actions
// in CActList's list of actions
// Parameters:
//		CActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
inline int CParser::HList(CActionList& al, int level, int sublevel)
{
	if (level >= LV_LARTICLE)
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LARTICLE_STATEMENTS, lex.prevLine(), lex.prevColumn());
	long int lines = 0, columns = 0;
	RequireAtom(l);
	// check for List attributes
	while (dynamic_cast<const CStatement*>(l->atom()) == NULL)
	{
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_DEFAULT);
		if (case_comp(l->atom()->identifier(), "length") == 0 && lines > 0)
			SetPError(parse_err, PERR_ATTRIBUTE_REDEF, MODE_PARSE, "",
			          lex.prevLine(), lex.prevColumn());
		if (case_comp(l->atom()->identifier(), "columns") == 0 && columns > 0)
			SetPError(parse_err, PERR_ATTRIBUTE_REDEF, MODE_PARSE, "",
			          lex.prevLine(), lex.prevColumn());
		string attr_name = l->atom()->identifier();
		RequireAtom(l);
		ValidateDType(l, attr);
		if (case_comp(attr_name, "length") == 0)
			lines = strtol(l->atom()->identifier().c_str(), 0, 10);
		else
			columns = strtol(l->atom()->identifier().c_str(), 0, 10);
		RequireAtom(l);
	}
	// check for modifier (Issue, Section, Article, SearchResult, Subtitle)
	st = (const CStatement*)l->atom();
	if (!CActList::validModifier(st->id()))
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, LvListSt(level),
		            lex.prevLine(), lex.prevColumn());
	}
	if (st->id() == CMS_ST_ISSUE && level >= LV_LISSUE)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvListSt(level), lex.prevLine(), lex.prevColumn());
	}
	if (st->id() == CMS_ST_SECTION && level >= LV_LSECTION)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvListSt(level), lex.prevLine(), lex.prevColumn());
	}
	if (st->id() == CMS_ST_SUBTITLE && (sublevel & SUBLV_WITH) == 0)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, LvListSt(level),
		            lex.prevLine(), lex.prevColumn());
	}
	const CStatement* mod_st = st;
	int mod = st->id();
	// check for modifier attributes
	CParameterList params;
	l = lex.getLexem();
	DEBUGLexem("hlist1", l);
	while (l->res() == CMS_LEX_IDENTIFIER && mod != CMS_ST_SEARCHRESULT && mod != CMS_ST_SUBTITLE)
	{
		StringSet ah;
		StringSet::iterator ah_i;
		StringSet keywords;
		CheckForAtom(l);
		string type;
		if (st->findType(l->atom()->identifier()))
		{
			type = l->atom()->identifier();
			RequireAtom(l);
		}
		SafeAutoPtr<CPairAttrType> attrType(NULL);
		if (type != "")
			attrType.reset(st->findTypeAttr(l->atom()->identifier(), type, CMS_CT_LIST));
		else
			attrType.reset(st->findAnyAttr(l->atom()->identifier(), CMS_CT_LIST));
		if (attrType->second != NULL)
			type = attrType->second->name();
		attr = attrType->first;
		if (case_comp(l->atom()->identifier(), "keyword")
		    && case_comp(l->atom()->identifier(), "OnFrontPage")
		    && case_comp(l->atom()->identifier(), "OnSection"))
		{
			if ((ah_i = ah.find(l->atom()->identifier())) != ah.end())
				SetPError(parse_err, PERR_ATTRIBUTE_REDEF, MODE_PARSE, "",
				          lex.prevLine(), lex.prevColumn());
			ah.insert(attr->identifier());
			if (attr->dataType() == CMS_DT_NONE)
			{
				params.insert(params.end(), new CParameter(attr->attribute()));
				l = lex.getLexem();
				DEBUGLexem("hlist2", l);
				continue;
			}
			RequireAtom(l);
			ValidateOperator(l, attr);
			string op = l->atom()->identifier();
			RequireAtom(l);
			if (attr->attrClass() != CMS_NORMAL_ATTR)
			{
				if (!st->findType(l->atom()->identifier()))
					throw InvalidType();
			}
			else
				ValidateDType(l, attr);
			params.insert(params.end(), new CParameter(attr->attribute(), type,
			                                  attr->compOperation(op, l->atom()->identifier())));
		}
		else
		{
			if (case_comp(attr->identifier(), "keyword") == 0)
			{
				RequireAtom(l);
			    if (keywords.find(l->atom()->identifier()) == keywords.end())
			    {
			    	keywords.insert(l->atom()->identifier());
					params.insert(params.end(), new CParameter("keyword", "",
					                   attr->compOperation(g_coEQUAL, l->atom()->identifier())));
				}
				l = lex.getLexem();
				DEBUGLexem("hlist4", l);
				continue;
			}
			string op = g_coEQUAL;
			string val = Switch::valName(Switch::ON);
			l = lex.getLexem();
			DEBUGLexem("hlist5", l);
			if (l->atom() != 0)
			{
				ValidateOperator(l, attr);
				op = l->atom()->identifier();
				RequireAtom(l);
				ValidateDType(l, attr);
				val = l->atom()->identifier();
			}
			params.insert(params.end(), new CParameter(attr->attribute(), "",
			                                           attr->compOperation(op, val)));
		}
		l = lex.getLexem();
		DEBUGLexem("hlist3", l);
	}
	// check for order params (Article, SearchResult, Issue)
	CParameterList ord_params;
	if ((st->id() == CMS_ST_ARTICLE || st->id() == CMS_ST_SEARCHRESULT
	     || st->id() == CMS_ST_ISSUE)
	    && l->res() == CMS_LEX_STATEMENT)
	{
		CheckForAtom(l);
		CheckForAtomType(l->atom(), const CStatement*, PERR_ATOM_NOT_STATEMENT,
		                 ST_ORDER, lex.prevLine(), lex.prevColumn());
		if (CMS_ST_ORDER != ((const CStatement*)l->atom())->id())
		{
			SetPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, ST_ORDER,
			          lex.prevLine(), lex.prevColumn());
		}
		st = CLex::findSt(l->atom()->identifier());
		RequireAtom(l);
		while (l->res() == CMS_LEX_IDENTIFIER)
		{
			StringSet ah;
			StringSet::iterator ah_i;
			CheckForAtom(l);
			attr = st->findAttr(l->atom()->identifier(), CMS_CT_LIST);
			if ((ah_i = ah.find(l->atom()->identifier())) != ah.end())
				SetPError(parse_err, PERR_ATTRIBUTE_REDEF, MODE_PARSE, "",
				          lex.prevLine(), lex.prevColumn());
			ah.insert(attr->identifier());
			RequireAtom(l);
			ValidateDType(l, attr);
			ord_params.insert(ord_params.end(), new CParameter(attr->attribute(), "", NULL,
			                                                   l->atom()->identifier()));
			l = lex.getLexem();
			if (l->res() != CMS_LEX_IDENTIFIER)
				break;
		}
	}
	if (l->res() != CMS_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	SafeAutoPtr<CActList> lal(new CActList(mod, lines, columns, params, ord_params));
	int res;
	sublevel &= (~SUBLV_IFPREV & ~SUBLV_IFNEXT & ~SUBLV_IFLIST
	             & ~SUBLV_EMPTYLIST & ~SUBLV_LOCAL);
	if (mod == CMS_ST_SEARCHRESULT)
		sublevel |= SUBLV_SEARCHRESULT;
	int tmp_level = LMod2Level(mod);
	if (tmp_level == 0)
		tmp_level = level;
	if ((res = LevelParser(lal->first_block, tmp_level, sublevel)))
	{
		return res;
	}
	int found_fel = 0;
	if (st->id() == CMS_ST_FOREMPTYLIST)
	{
		WaitForStatementEnd(true);
		if ((res = LevelParser(lal->second_block, LMod2Level(mod), sublevel | SUBLV_EMPTYLIST)))
		{
			return res;
		}
		found_fel = 1;
	}
	if (st->id() != CMS_ST_ENDLIST)
	{
		string exp = ST_ENDLIST;
		if (!found_fel)
			exp += string(", ") + ST_FOREMPTYLIST;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, exp,
		            lex.prevLine(), lex.prevColumn());
	}
	al.insert(al.end(), lal.release());
	l = lex.getLexem();
	DEBUGLexem("hlist4", l);
	if (l->res() == CMS_LEX_END_STATEMENT)
		return 0;
	string req_s = string(mod_st->identifier()) + ", >";
	CheckForStatement(l, req_s, lex.prevLine(), lex.prevColumn());
	CheckForAtom(l);
	CheckForAtomType(l->atom(), const CStatement*, PERR_ATOM_NOT_STATEMENT,
	                 req_s, lex.prevLine(), lex.prevColumn());
	if (mod_st->id() != ((const CStatement*)l->atom())->id())
		SetPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, req_s,
		          lex.prevLine(), lex.prevColumn());
	WaitForStatementEnd(true);
	return 0;
}

// HIf: parse if statement; add CActIf action to actions list (al)
// All statements between If and EndIf statements are parsed, added as actions
// in CActIf's list of actions
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HIf(CActionList& al, int lv, int sublv)
{
	CParameter param("");
	IntSet rc_hash;
	bool bNegated = false;
	RequireAtom(l);
	if (case_comp(l->atom()->identifier(), "not") == 0)
	{
		bNegated = true;
		RequireAtom(l);
	}
	CheckForAtomType(l->atom(), const CStatement*, PERR_ATOM_NOT_STATEMENT,
	                 IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
	st = CLex::findSt(l->atom()->identifier());
	const CStatement* mod_st = st;
	if (!CActIf::validModifier(st->id()))
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
	}
	if (st->id() == CMS_ST_ALLOWED)
	{
		sublv |= SUBLV_IFALLOWED;
	}
	else if (st->id() == CMS_ST_SUBSCRIPTION)
	{
		RequireAtom(l);
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_IF);
		param = CParameter(l->atom()->identifier());
	}
	else if (st->id() == CMS_ST_USER)
	{
		if (sublv & SUBLV_USER)
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
		}
		RequireAtom(l);
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_IF);
		param = CParameter(l->atom()->identifier());
	}
	else if (st->id() == CMS_ST_LOGIN)
	{
		if (sublv & SUBLV_LOGIN)
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
		}
		RequireAtom(l);
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_IF);
		param = CParameter(l->atom()->identifier());
	}
	else if (st->id() == CMS_ST_SEARCH)
	{
		if (sublv & SUBLV_SEARCH)
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
		}
		RequireAtom(l);
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_IF);
		param = CParameter(l->atom()->identifier());
	}
	else if (st->id() == CMS_ST_PREVIOUSITEMS)
	{
		if (sublv & SUBLV_EMPTYLIST || sublv & SUBLV_IFPREV
		        || sublv & SUBLV_IFNEXT
		        || (lv == LV_ROOT && (sublv & SUBLV_SEARCHRESULT) == 0))
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
		}
	}
	else if (st->id() == CMS_ST_NEXTITEMS)
	{
		if (sublv & SUBLV_EMPTYLIST || sublv & SUBLV_IFPREV
		        || sublv & SUBLV_IFNEXT
		        || (lv == LV_ROOT && (sublv & SUBLV_SEARCHRESULT) == 0))
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
		}
	}
	else if (st->id() == CMS_ST_CURRENTSUBTITLE)
	{
		if (!((lv & LV_LSUBTITLE ) && (sublv & SUBLV_WITH )))
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
		}
	}
	else if (st->id() == CMS_ST_SUBTITLE)
	{
		if (sublv & SUBLV_WITH == 0)
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
		}
		RequireAtom(l);
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_IF);
		RequireAtom(l);
		ValidateDType(l, attr);
		param = CParameter(attr->identifier(), "",
		                   attr->compOperation(g_coEQUAL, l->atom()->identifier()));
	}
	else if (st->id() == CMS_ST_LIST)
	{
		if ((sublv & SUBLV_EMPTYLIST)
		        || (lv == LV_ROOT && (sublv & SUBLV_SEARCHRESULT) == 0))
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.prevLine(), lex.prevColumn());
		}
		RequireAtom(l);
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_IF);
		sublv |= SUBLV_IFLIST;
		param = CParameter(attr->identifier());
		bool first = true;
		do
		{
			if (case_comp(attr->identifier(), "start") == 0
			    || case_comp(attr->identifier(), "end") == 0)
			{
				break;
			}
			l = lex.getLexem();
			DEBUGLexem("hif1", l);
			if (l->res() != CMS_LEX_IDENTIFIER)
			{
				if (first == true)
					FatalPError(parse_err, PERR_IDENTIFIER_MISSING, MODE_PARSE,
					            attr->typeNameValues(), lex.prevLine(), lex.prevColumn());
				break;
			}
			CheckForAtom(l);
			if (l->dataType() != CMS_DT_INTEGER)
			{
				if (param.spec() != "")
					FatalPError(parse_err, PERR_INVALID_VALUE, MODE_PARSE,
					            "number", lex.prevLine(), lex.prevColumn());
				param = CParameter(attr->identifier(), "", NULL, l->atom()->identifier());
			}
			else
				rc_hash.insert(strtol(l->atom()->identifier().c_str(), 0, 10));
			first = false;
		} while (1);
	}
	else if (st->id() == CMS_ST_IMAGE)
	{
		RequireAtom(l);
		if (l->dataType() != CMS_DT_INTEGER)
		{
			SetPError(parse_err, PERR_DATA_TYPE, MODE_PARSE, "integer",
			          lex.prevLine(), lex.prevColumn());
			return 1;
		}
		param = CParameter(l->atom()->identifier());
	}
	else if (st->id() == CMS_ST_ISSUE)
	{
		RequireAtom(l);
		attr = st->findAttr(l->atom()->identifier(), CMS_CT_IF);
		if (attr->dataType() != CMS_DT_NONE)
		{
			RequireAtom(l);
			ValidateOperator(l, attr);
			string op = l->atom()->identifier();
			RequireAtom(l);
			ValidateDType(l, attr);
			param = CParameter(attr->attribute(), "",
			                   attr->compOperation(op, l->atom()->identifier()));
		}
		else
			param = CParameter(attr->attribute());
		sublv |= SUBLV_IFISSUE;
	}
	else if (st->id() == CMS_ST_SECTION || st->id() == CMS_ST_ARTICLE
	         || st->id() == CMS_ST_LANGUAGE || st->id() == CMS_ST_PUBLICATION)
	{
		RequireAtom(l);
		string type;
		if (st->findType(l->atom()->identifier()))
		{
			type = l->atom()->identifier();
			RequireAtom(l);
		}
		SafeAutoPtr<CPairAttrType> anyAttr(NULL);
		if (type != "")
			anyAttr.reset(st->findTypeAttr(l->atom()->identifier(), type, CMS_CT_IF));
		else
			anyAttr.reset(st->findAnyAttr(l->atom()->identifier(), CMS_CT_IF));
		attr = anyAttr->first;
		if (anyAttr->second)
			type = anyAttr->second->name();
		if (attr->dataType() != CMS_DT_NONE)
		{
			RequireAtom(l);
			if (attr->attrClass() == CMS_TYPE_ATTR)
			{
				if (!st->findType(l->atom()->identifier()))
					throw InvalidType();
				param = CParameter(attr->attribute(), "",
				                   attr->compOperation(g_coEQUAL, l->atom()->identifier()));
			}
			else
			{
				ValidateOperator(l, attr);
				string op = l->atom()->identifier();
				RequireAtom(l);
				ValidateDType(l, attr);
				param = CParameter(attr->attribute(), type,
				                   attr->compOperation(op, l->atom()->identifier()));
			}
		}
		else
		{
			string spec;
			if (case_comp(attr->identifier(), "has_keyword") == 0
			    || case_comp(attr->identifier(), "translated_to") == 0)
			{
				RequireAtom(l);
				spec = l->atom()->identifier();
			}
			param = CParameter(attr->attribute(), "", NULL, spec);
		}
		if (st->id() == CMS_ST_SECTION)
		{
			sublv |= SUBLV_IFSECTION;
		}
		else if (st->id() == CMS_ST_ARTICLE)
		{
			sublv |= SUBLV_IFARTICLE;
		}
		else if (st->id() == CMS_ST_LANGUAGE)
		{
			sublv |= SUBLV_IFLANGUAGE;
		}
		else if (st->id() == CMS_ST_PUBLICATION)
		{
			sublv |= SUBLV_IFPUBLICATION;
		}
	}
	if (l->res() != CMS_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	SafeAutoPtr<CActIf> ai(new CActIf(st->id(), param, bNegated));
	ai->rc_hash = rc_hash;
	int res;
	if ((res = LevelParser(ai->block, lv, sublv)))
	{
		return res;
	}
	if (st->id() == CMS_ST_ELSE)
	{
		WaitForStatementEnd(true);
		if ((res = LevelParser(ai->sec_block, lv, sublv)))
		{
			return res;
		}
	}
	if (st->id() != CMS_ST_ENDIF)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDIF, lex.prevLine(), lex.prevColumn());
	}
	al.insert(al.end(), ai.release());
	l = lex.getLexem();
	DEBUGLexem("hif2", l);
	if (l->res() != CMS_LEX_END_STATEMENT)
	{
		CheckForAtom(l);
		CheckForAtomType(l->atom(), const CStatement*, PERR_ATOM_NOT_STATEMENT,
		                 mod_st->identifier(), lex.prevLine(), lex.prevColumn());
		if (mod_st->id() != ((const CStatement*)(l->atom()))->id())
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            mod_st->identifier(), lex.prevLine(), lex.prevColumn());
		WaitForStatementEnd(true);
	}
	return 0;
}

// HLocal: parse local statement; add CActLocal action to actions list (al)
// All statements between Local and EndLocal statements are parsed, added as actions
// in CActLocal's list of actions
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HLocal(CActionList& al, int lv, int sublv)
{
	int res;
	WaitForStatementEnd(true);
	SafeAutoPtr<CActLocal> aloc(new CActLocal());
	if ((res = LevelParser(aloc->block, lv, sublv | SUBLV_LOCAL)))
	{
		return res;
	}
	if (st->id() != CMS_ST_ENDLOCAL)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDLOCAL, lex.prevLine(), lex.prevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), aloc.release());
	return 0;
}

// HSubscription: parse subscription statement; add CActSubscription action to actions
// list (al)
// All statements between Subscription and EndSubscription statements are parsed,
// added as actions in CActSubscription's list of actions
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HSubscription(CActionList& al, int lv, int sublv)
{
	if (sublv & SUBLV_USER || sublv & SUBLV_LOGIN || sublv & SUBLV_SUBSCRIPTION)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvStatements(lv), lex.prevLine(), lex.prevColumn());
	}
	string tpl_file, unit_name, button_name, total, evaluate;
	bool by_publication;
	RequireAtom(l);
	attr = st->findAttr(l->atom()->identifier(), CMS_CT_DEFAULT);
	by_publication = case_comp(attr->identifier(), "by_publication") == 0;
	RequireAtom(l);
	tpl_file = l->atom()->identifier();
	RequireAtom(l);
	button_name = l->atom()->identifier();
	l = lex.getLexem();
	DEBUGLexem("hsubs atom", l);
	CheckForEOF(l, PERR_EOS_MISSING);
	if (l->res() != CMS_LEX_END_STATEMENT)
	{
		CheckForAtom(l);
		total = l->atom()->identifier();
		RequireAtom(l);
		evaluate = l->atom()->identifier();
	}
	int res;
	if (l->res() != CMS_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	SafeAutoPtr<CActSubscription> aloc(new CActSubscription(by_publication, tpl_file, button_name,
	                                                        total, evaluate));
	sublv |= SUBLV_SUBSCRIPTION;
	if ((res = LevelParser(aloc->block, lv, sublv)))
	{
		return res;
	}
	if (st->id() != CMS_ST_ENDSUBSCRIPTION)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDSUBSCRIPTION, lex.prevLine(), lex.prevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), aloc.release());
	return 0;
}

// HEdit: parse edit statement; add CActEdit action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HEdit(CActionList& al, int lv, int sublv)
{
	string size;
	RequireAtom(l);
	CheckForStatement(l, EditStatements(sublv), lex.prevLine(), lex.prevColumn());
	st = CLex::findSt(l->atom()->identifier());
	if (!CActEdit::validModifier(st->id()))
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            EditStatements(sublv), lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	attr = st->findAttr(l->atom()->identifier(), CMS_CT_EDIT);
	if (st->id() == CMS_ST_SUBSCRIPTION)
	{
		if ((sublv & SUBLV_SUBSCRIPTION) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            EditStatements(sublv), lex.prevLine(), lex.prevColumn());
	}
	else if (st->id() == CMS_ST_USER)
	{
		if ((sublv & SUBLV_USER) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            EditStatements(sublv), lex.prevLine(), lex.prevColumn());
	}
	else if (st->id() == CMS_ST_LOGIN)
	{
		if ((sublv & SUBLV_LOGIN) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            EditStatements(sublv), lex.prevLine(), lex.prevColumn());
	}
	else if (st->id() == CMS_ST_SEARCH)
	{
		if ((sublv & SUBLV_SEARCH) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            EditStatements(sublv), lex.prevLine(), lex.prevColumn());
		l = lex.getLexem();
		DEBUGLexem("edit", l);
		if (l->res() != CMS_LEX_END_STATEMENT)
		{
			CheckForAtom(l);
			if (l->dataType() != CMS_DT_INTEGER)
			{
				SetPError(parse_err, PERR_DATA_TYPE, MODE_PARSE, "integer",
				          lex.prevLine(), lex.prevColumn());
				return PERR_INVALID_VALUE;
			}
			size = l->atom()->identifier();
		}
	}
	CActEdit* edit = new CActEdit(st->id(), attr->attribute(), atol(size.c_str()));
	al.insert(al.end(), edit);
	if (l->res() != CMS_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HSelect: parse select statement; add CActSelect action to actions list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HSelect(CActionList& al, int lv, int sublv)
{
	string male_name, female_name;
	bool checked = false;
	const CLexem *l;
	RequireAtom(l);
	CheckForStatement(l, ST_SUBSCRIPTION ", " ST_USER ", " ST_SEARCH,
	                  lex.prevLine(), lex.prevColumn());
	st = CLex::findSt(l->atom()->identifier());
	if (!CActSelect::validModifier(st->id()))
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, ST_SUBSCRIPTION,
		            lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	attr = st->findAttr(l->atom()->identifier(), CMS_CT_SELECT);
	if (st->id() == CMS_ST_SUBSCRIPTION)
	{
		if ((sublv & SUBLV_SUBSCRIPTION) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            SelectStatements(sublv), lex.prevLine(), lex.prevColumn());
	}
	else if (st->id() == CMS_ST_USER)
	{
		if ((sublv & SUBLV_USER) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            SelectStatements(sublv), lex.prevLine(), lex.prevColumn());
		if (case_comp(attr->identifier(), "Gender") == 0)
		{
			RequireAtom(l);
			male_name = l->atom()->identifier();
			RequireAtom(l);
			female_name = l->atom()->identifier();
		}
		else if (case_comp(attr->identifier(), "Pref", 4) == 0)
		{
			l = lex.getLexem();
			CheckForEOF(l, PERR_EOS_MISSING);
			if (l->res() != CMS_LEX_END_STATEMENT)
			{
				CheckForAtom(l);
				if (case_comp(l->atom()->identifier(), "checked") == 0)
					checked = true;
			}
		}
	}
	else if (st->id() == CMS_ST_SEARCH)
	{
		if ((sublv & SUBLV_SEARCH) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            SelectStatements(sublv), lex.prevLine(), lex.prevColumn());
	}
	CActSelect* select = new CActSelect(st->id(), attr->attribute(), male_name, female_name, checked);
	al.insert(al.end(), select);
	if (l->res() != CMS_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HUser: parse user statement; add CActUser action to actions list (al)
// All statements between User and EndUser statements are parsed, added as actions
// in CActUsers's list of actions
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HUser(CActionList& al, int lv, int sublv)
{
	if (sublv & SUBLV_USER || sublv & SUBLV_SUBSCRIPTION || sublv & SUBLV_LOGIN)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvStatements(lv), lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	bool add = case_comp(l->atom()->identifier(), "add") == 0;
	RequireAtom(l);
	string tpl_file = l->atom()->identifier();
	RequireAtom(l);
	string button_name = l->atom()->identifier();
	int res;
	WaitForStatementEnd(true);
	SafeAutoPtr<CActUser> user(new CActUser(add, tpl_file, button_name));
	if ((res = LevelParser(user->block, lv, sublv | SUBLV_USER)))
	{
		return res;
	}
	if (st->id() != CMS_ST_ENDUSER)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDUSER, lex.prevLine(), lex.prevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), user.release());
	return 0;
}

// HLogin: parse login statement; add CActLogin action to actions list (al)
// All statements between Login and EndLogin statements are parsed, added as actions
// in CActLogin's list of actions
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HLogin(CActionList& al, int lv, int sublv)
{
	if (sublv & SUBLV_USER || sublv & SUBLV_SUBSCRIPTION || sublv & SUBLV_LOGIN)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvStatements(lv), lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	string tpl_file = l->atom()->identifier();
	RequireAtom(l);
	string button_name = l->atom()->identifier();
	int res;
	WaitForStatementEnd(true);
	SafeAutoPtr<CActLogin> login(new CActLogin(tpl_file, button_name));
	if ((res = LevelParser(login->block, lv, sublv | SUBLV_LOGIN)))
	{
		return res;
	}
	if (st->id() != CMS_ST_ENDLOGIN)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDUSER, lex.prevLine(), lex.prevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), login.release());
	return 0;
}

// HSearch: parse search statement; add CActSearch action to actions list (al)
// All statements between Search and EndSearch statements are parsed, added as actions
// in CActSearch's list of actions
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HSearch(CActionList& al, int lv, int sublv)
{
	if (sublv & SUBLV_SEARCH)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvStatements(lv), lex.prevLine(), lex.prevColumn());
	}
	RequireAtom(l);
	string tpl_file = l->atom()->identifier();
	RequireAtom(l);
	string button_name = l->atom()->identifier();
	int res;
	WaitForStatementEnd(true);
	SafeAutoPtr<CActSearch> search(new CActSearch(tpl_file, button_name));
	if ((res = LevelParser(search->block, lv, sublv | SUBLV_SEARCH)))
	{
		return res;
	}
	if (st->id() != CMS_ST_ENDSEARCH)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDSEARCH, lex.prevLine(), lex.prevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), search.release());
	return 0;
}

// HWith: parse with statement; add CActWith action to actions list (al)
// All statements between With and EndWith statements are parsed, added as actions
// in CActWith's list of actions
// Parameters:
//		CActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int CParser::HWith(CActionList& al, int lv, int sublv)
{
	int res;
	SafeAutoPtr<CPairAttrType> a(NULL);
	RequireAtom(l);
	const CStatement* pcoArtSt = CLex::findSt(ST_ARTICLE);
	if (pcoArtSt == NULL)
		throw InvalidType();
	if (!pcoArtSt->findType(l->atom()->identifier()))
		throw InvalidType();
	string art_type = l->atom()->identifier();
	RequireAtom(l);
	a.reset(pcoArtSt->findTypeAttr(l->atom()->identifier(), art_type, CMS_CT_WITH));
	string field = l->atom()->identifier();
	WaitForStatementEnd(true);
	SafeAutoPtr<CActWith> aloc(new CActWith(art_type, field));
	if ((res = LevelParser(aloc->block, lv, sublv | SUBLV_WITH)))
	{
		return res;
	}
	if (st->id() != CMS_ST_ENDWITH)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDWITH, lex.prevLine(), lex.prevColumn());
	}
	WaitForStatementEnd(true);
	fields.insert(String2String::value_type(field, art_type));
	al.insert(al.end(), aloc.release());
	return 0;
}

// LevelParser: read lexems until it finds a statement or reaches end of file
// Depending on read statement it calls on of HArticle, HDate, HEdit, HFormParameters,
// HIf, HInclude, HIssue, HLanguage, HList, HLocal, HLogin, HPrint, HPublication,
// HSearch, HSection, HSelect, HSubscription, HURLParameters, HUser, HWith methods.
// It does not add actions to action list (al)
// Parameters:
//		CActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
int CParser::LevelParser(CActionList& al, int level, int sublevel)
{
	bool isEOF = false;
	int res, context;
	while (isEOF == false)
	{
		OPEN_TRY
		attr = NULL;
		st = NULL;
		l = WaitForStatementStart(al);
		int err = level == LV_ROOT ? RES_OK : PERR_UNEXPECTED_EOF;
		CheckForEOF(l, err);
		RequireAtom(l);
		if (dynamic_cast<const CStatement*> (l->atom()) == NULL)
		{
			SetPError(parse_err, PERR_ATOM_NOT_STATEMENT, MODE_PARSE,
			          LvStatements(level), lex.prevLine(), lex.prevColumn());
			WaitForStatementEnd(false);
			continue;
		}
		st = (const CStatement*)l->atom();
		context = st->id();
		switch (st->id())
		{
		case CMS_ST_LANGUAGE:
			HLanguage(al, level, sublevel);
			break;
		case CMS_ST_INCLUDE:
			HInclude(al);
			break;
		case CMS_ST_PUBLICATION:
			DEBUGLexem("hpublication 1", l);
			HPublication(al, level, sublevel);
			break;
		case CMS_ST_ISSUE:
			HIssue(al, level, sublevel);
			break;
		case CMS_ST_SECTION:
			HSection(al, level, sublevel);
			break;
		case CMS_ST_ARTICLE:
			HArticle(al, level, sublevel);
			break;
		case CMS_ST_LIST:
			if ((res = HList(al, level, sublevel)))
				return res;
			break;
		case CMS_ST_URLPARAMETERS:
			HURLParameters(al);
			break;
		case CMS_ST_FORMPARAMETERS:
			HFormParameters(al);
			break;
		case CMS_ST_PRINT:
			HPrint(al, level, sublevel);
			break;
		case CMS_ST_IF:
			if ((res = HIf(al, level, sublevel)))
				return res;
			break;
		case CMS_ST_DATE:
			HDate(al);
			break;
		case CMS_ST_LOCAL:
			if ((res = HLocal(al, level, sublevel)))
				return res;
			break;
		case CMS_ST_SUBSCRIPTION:
			if ((res = HSubscription(al, level, sublevel)))
				return res;
			break;
		case CMS_ST_EDIT:
			HEdit(al, level, sublevel);
			break;
		case CMS_ST_SELECT:
			HSelect(al, level, sublevel);
			break;
		case CMS_ST_USER:
			if ((res = HUser(al, level, sublevel)))
				return res;
			break;
		case CMS_ST_LOGIN:
			if ((res = HLogin(al, level, sublevel)))
				return res;
			break;
		case CMS_ST_SEARCH:
			if ((res = HSearch(al, level, sublevel)))
				return res;
			break;
		case CMS_ST_WITH:
			if ((res = HWith(al, level, sublevel)))
				return res;
			break;
		case CMS_ST_FOREMPTYLIST:
		case CMS_ST_ELSE:
		case CMS_ST_ENDIF:
		case CMS_ST_ENDLIST:
		case CMS_ST_ENDLOCAL:
		case CMS_ST_ENDSUBSCRIPTION:
		case CMS_ST_ENDUSER:
		case CMS_ST_ENDLOGIN:
		case CMS_ST_ENDSEARCH:
		case CMS_ST_ENDWITH:
			return 0;
		default:
			SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,
			          LvStatements(level), lex.prevLine(), lex.prevColumn());
		}
		CLOSE_TRY
		CATCH(InvalidValue)
			SetPError(parse_err, PERR_INVALID_VALUE, MODE_PARSE, attr ? attr->typeValues() : "",
			          lex.prevLine(), lex.prevColumn());
		END_CATCH
		CATCH(InvalidOperator)
			SetPError(parse_err, PERR_INVALID_OPERATOR, MODE_PARSE, attr ? attr->operators() : "",
			          lex.prevLine(), lex.prevColumn());
		END_CATCH
		CATCH(InvalidAttr)
			string a_req;
			if (st)
			{
				if (rcoEx.mode() & g_nFIND_NORMAL)
					a_req = st->contextAttrs(context);
				if (rcoEx.mode() & g_nFIND_TYPE)
				{
					string types;
					if (rcoEx.type() != "")
						types = st->types();
					string typeAttrs = st->typeAttrs(rcoEx.type(), context);
					if (types != "" && a_req != "")
						a_req += ", " + types;
					if (typeAttrs != "" && a_req != "")
						a_req += ", " + typeAttrs;
				}
			}
			SetPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE,
			          a_req, lex.prevLine(), lex.prevColumn());
		END_CATCH
		CATCH(InvalidType)
			SetPError(parse_err, PERR_INV_TYPE_VAL, MODE_PARSE,
			          st ? st->types() : "", lex.prevLine(), lex.prevColumn());
		END_CATCH
		CATCH(InvalidModifier)
			SetPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, "",
			          lex.prevLine(), lex.prevColumn());
		END_CATCH
		CATCH(InvalidPointer)
			FatalPError(parse_err, ERR_NOMEM, MODE_PARSE, "", lex.prevLine(), lex.prevColumn());
		END_CATCH
		CATCH(bad_alloc)
			FatalPError(parse_err, ERR_NOMEM, MODE_PARSE, "", lex.prevLine(), lex.prevColumn());
		END_CATCH
		CATCH(InvalidOperation)
			SetPError(parse_err, PERR_INTERNAL, MODE_PARSE, "", lex.prevLine(), lex.prevColumn());
		END_CATCH
		CATCH(ExMutex)
			SetPError(parse_err, PERR_INTERNAL, MODE_PARSE, "", lex.prevLine(), lex.prevColumn());
		END_CATCH
	}
	return 0;
}

// CParser: constructor
// Parameters:
//		const string& p_rcoTpl - template path
//		const string& dr - document root
CParser::CParser(const string& p_rcoTpl, const string& dr)
{
	FUNC_DEBUG("CParser::CParser", tpl);
	CRWMutexHandler h(&m_coOpMutex, true);
	tpl = p_rcoTpl;
	parent_tpl.insert(tpl);
	document_root = dr;
	parsed = false;
	parse_err_printed = false;
	write_err_printed = false;
	m_nTplFileLen = 0;
	m_nTplFD = -1;
	m_pchTplBuf = NULL;
	memset((void*)&tpl_stat, 0, sizeof(tpl_stat));
	MapTpl();
	UnMapTpl();
	CMutexHandler mh(&m_coMapMutex);
	m_coPMap.Insert(this);
}

// copy-constructor
CParser::CParser(const CParser&)
{
	throw exception();
}

// destructor
CParser::~CParser()
{
	try
	{
		FUNC_DEBUG("CParser::~CParser", tpl);
		CRWMutexHandler h(&m_coOpMutex, true);
		reset();
		UnMapTpl();
		CMutexHandler mh(&m_coMapMutex);
		m_coPMap.Erase(this);
	}
	catch (...)
	{
	}
}

// setMYSQL: set MySQL connection
// Parameters:
//		MYSQL* p_pMYSQL - pointer to MySQL server connection
void CParser::setMYSQL(MYSQL* p_pMYSQL)
{
	s_MYSQL = p_pMYSQL;
	CAction::setSql(p_pMYSQL);
}

// assign operator
const CParser& CParser::operator =(const CParser&)
{
	throw exception();
}

// reset: reset parser: clear actions tree, reset lex, clear errors list
void CParser::reset()
{
	FUNC_DEBUG("CParser::reset", tpl);
	CRWMutexHandler h(&m_coOpMutex, true);
	parse_err.clear();
	write_err.clear();
	child_tpl.clear();
	lex.reset(m_pchTplBuf, m_nTplFileLen);
	if (tpl != "")
		parent_tpl.insert(tpl);
	parse_err_printed = false;
	write_err_printed = false;
	actions.clear();
	parsed = false;
}

// resetMap: reset all the parsers in the map
void CParser::resetMap()
{
	CMutexHandler mh(&m_coMapMutex);
	m_coPMap.Reset();
}

// parserOf: return pointer to parser of template
CParser* CParser::parserOf(const string& p_rcoTpl, const string& p_rcoDocRoot)
{
	CParser* p = m_coPMap.find(p_rcoTpl);
	if (p)
		return p;
	return new CParser(p_rcoTpl, p_rcoDocRoot);
}

// parse: start the parser; return the parse result
// Parameters:
//		bool force = false - if true, force reparsing of template; if false, do not
//			parse the template again if already parsed
int CParser::parse(bool force)
{
	FUNC_DEBUG("CParser::parse", tpl);
	CRWMutexHandler h(&m_coOpMutex, false);
	MapTpl();
	if (!parsed || force)
	{
		CRWMutexHandler h2(&m_coOpMutex, true);
		reset();
		int nRetVal = LevelParser(actions, LV_ROOT, SUBLV_NONE);
		parsed = true;
		return nRetVal;
	}
	return 0;
}

// writeOutput: write actions output to given file stream
// Parameters:
//		const CContext& c - context
//		fstream& fs - output file stream
int CParser::writeOutput(const CContext& c, fstream& fs)
{
	FUNC_DEBUG("CParser::writeOutput", tpl);
	CRWMutexHandler h(&m_coOpMutex, false);
	MapTpl();
	if (!parsed)
		parse();
	CActionList::iterator al_i;
	write_err.clear();
	CContext lc = c;
	lc.SetLevel(CLV_ROOT);
	String2String::iterator it;
	for (it = fields.begin(); it != fields.end(); ++it)
		lc.SetField((*it).first, (*it).second);
	if (debug())
		fs << "<!-- start template " << tpl << " -->" << endl;
	int nRetVal = 0;
	for (al_i = actions.begin(); al_i != actions.end(); ++al_i)
	{
		int err;
		if (debug())
			fs << "<!-- taking action " << (*al_i)->actionType() << " -->\n";
		try
		{
			if ((err = (*al_i)->takeAction(lc, fs)) != RES_OK)
				SetPError(write_err, err, MODE_WRITE, (*al_i)->actionType(), 0, 0);
		}
		catch (InvalidOperation& rcoEx)
		{
			SetPError(write_err, err, MODE_WRITE, (*al_i)->actionType(), 0, 0);
		}
		if (debug())
			fs << "<!-- action " << (*al_i)->actionType() << " result: " << err << " -->\n";
		nRetVal |= err;
	}
	if (debug())
		fs << "<!-- end template " << tpl << " -->" << endl;
	return RES_OK;
}

// printParseErrors: print parse errors to given output stream
// Parameters:
//		fstream& fs - output file stream
//		bool p_bMainTpl = false - true if this is the main template
void CParser::printParseErrors(fstream& fs, bool p_bMainTpl)
{
	FUNC_DEBUG("CParser::printParseErrors", tpl);
	CRWMutexHandler h(&m_coOpMutex, false);
	if (p_bMainTpl)
	{
		SetWriteErrors(true);
		fs << "<p><font color=blue><b><p>- in main template " << tpl << "</b></font><br>";
	}
	else
	{
		if (*parse_err_printed)
		{
			return ;
		}
		fs << "<p><font color=blue><b><p>- in included template " << tpl << "</b></font><br>";
	}
	CErrorList::iterator el_i;
	for (el_i = parse_err.begin(); el_i != parse_err.end(); ++el_i)
		(*el_i)->Print(fs);
	if (parse_err.empty())
		fs << "No errors found<p>";
	parse_err_printed = true;
	for (StringSet::iterator sh_i = child_tpl.begin(); sh_i != child_tpl.end(); ++sh_i)
	{
		if (*sh_i == tpl)
			continue;
		CParser* p;
		if ((p = m_coPMap.find(*sh_i)) != NULL)
			p->printParseErrors(fs);
	}
}

// printWriteErrors: print write errors to given output stream
// Parameters:
//		fstream& fs - output file stream
//		bool p_bMainTpl = false - true if this is the main template
void CParser::printWriteErrors(fstream& fs, bool p_bMainTpl)
{
	FUNC_DEBUG("CParser::printWriteErrors", tpl);
	CRWMutexHandler h(&m_coOpMutex, false);
	if (p_bMainTpl)
	{
		SetWriteErrors(true);
		fs << "<p>- on main template " << tpl << "<p>";
	}
	else
	{
		if (*write_err_printed)
		{
			return ;
		}
		fs << "<p>- on included template " << tpl << "<p>";
	}
	CErrorList::iterator el_i;
	for (el_i = write_err.begin(); el_i != write_err.end(); ++el_i)
		(*el_i)->Print(fs);
	if (write_err.empty())
		fs << "No errors found<p>";
	write_err_printed = true;
	for (StringSet::iterator sh_i = child_tpl.begin(); sh_i != child_tpl.end(); ++sh_i)
	{
		if (*sh_i == tpl)
			continue;
		CParser* p;
		if ((p = m_coPMap.find(*sh_i)) != NULL)
			p->printWriteErrors(fs);
	}
}

// testLex: test the lex; debug purposes only
void CParser::testLex()
{
	CRWMutexHandler h(&m_coOpMutex, false);
	MapTpl();
	cout << "START PARSED FILE\n";
	const CLexem *c_lexem;
	while (((c_lexem = lex.getLexem())->res()) != CMS_ERR_EOF)
	{
		if (c_lexem->atom())
			cout << "*[" << c_lexem->atom()->atomType()
			<< " \"" << c_lexem->atom()->identifier()
			<< "\" " << (int)(c_lexem->dataType()) << "]\n";
		else if (c_lexem->textStart())
		{
			cout << "%";
			cout.write(c_lexem->textStart(), c_lexem->textLen());
			cout << "% text len: " << c_lexem->textLen() << "\n";
		}
		else
			cout << "# received nothing #\n";
		cout << "$ lexem res: " << (int)(c_lexem->res()) << " $\n";
	}
	cout << "END PARSED FILE\n";
}

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
 
Contains the main function, initialisation functions and functions performing
certain operations against database: subscription, login, change user
information, search articles.

The main function builds the context from cgi parameters and from database,
calls the initialisation functions, eventually the functions performing
operations against database, creates a parser hash, creates a parser object
for the requested template and calls Parse and WriteOutput methods of parser
object.
 
******************************************************************************/

#include <sys/time.h>
#include <sys/types.h>
#include <unistd.h>
#include <string.h>
#include <iostream.h>
#include <fstream.h>
#include <signal.h>
#include <sys/wait.h>

#include "tol_lex.h"
#include "tol_atoms.h"
#include "tol_context.h"
#include "tol_parser.h"
#include "tol_util.h"
#include "sql_connect.h"
#include "cgi.h"
#include "threadpool.h"
#include "tol_types.h"
#include "tol_srvdef.h"
#include "csocket.h"

#define PARAM_NR 36
#define ERR_NR 6

// ExThread class; exception thrown by functions in main.cpp
class Exception
{
public:
	Exception(const char* p_pchMsg) : m_pchMsg(p_pchMsg)
	{}
	virtual ~Exception()
	{}

	const char* Message() const
	{
		return m_pchMsg;
	}

private:
	const char* m_pchMsg;
};

// CGIParams: structure containing some CGI environment variables
typedef struct CGIParams
{
	pChar m_pchDocumentRoot;
	pChar m_pchIP;
	pChar m_pchPathTranslated;
	pChar m_pchPathInfo;
	pChar m_pchRequestMethod;
	pChar m_pchQueryString;
	pChar m_pchHttpCookie;

	CGIParams()
			: m_pchDocumentRoot(NULL), m_pchIP(NULL), m_pchPathTranslated(NULL),
			m_pchPathInfo(NULL), m_pchRequestMethod(NULL), m_pchQueryString(NULL),
			m_pchHttpCookie(NULL)
	{}

	~CGIParams()
	{
		delete m_pchDocumentRoot;
		delete m_pchIP;
		delete m_pchPathTranslated;
		delete m_pchPathInfo;
		delete m_pchRequestMethod;
		delete m_pchQueryString;
		delete m_pchHttpCookie;
	}
} CGIParams;

// WriteCharset: write http tag specifying the charset - according to current language
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		fstream& fs - output stream
int WriteCharset(TOLContext& c, MYSQL* pSql, fstream& fs);

// Login: perform login action: log user in
// Parameters:
//		CGI& cgi - cgi environment
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int Login(CGI& cgi, TOLContext& c, MYSQL* pSql);

// CheckUserInfo: read user informations from CGI parameters
// Parameters:
//		CGI& cgi - cgi environment
//		TOLContext& c - current context
//		const char* ppchParams[] - parameters to read from cgi environment
//		int param_nr - parameters number
int CheckUserInfo(CGI& cgi, TOLContext& c, const char* ppchParams[], int param_nr);

// AddUser: perform add user action (add user to database); return error code
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		const char* ppchParams[] - parameters to read from context (user information)
//		int param_nr - parameters number
//		const int errs[] - error list (errors codes)
//		int err_nr - errors number
int AddUser(TOLContext& c, MYSQL* pSql, const char* ppchParams[], int param_nr,
			const int errs[], int err_nr);

// ModifyUser: perform modify user action (modify user information in the database)
// Return error code.
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		const char* ppchParams[] - parameters to read from context (user information)
//		int param_nr - parameters number
//		const int errs[] - error list (errors codes)
//		int err_nr - errors number
int ModifyUser(TOLContext& c, MYSQL* pSql, const char* ppchParams[], int param_nr,
               const int errs[], int err_nr);

// DoSubscribe: perform subscribe action (subscribe user to a certain publication)
// Parameters:
//		CGI& cgi - cgi environment
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int DoSubscribe(CGI& cgi, TOLContext& c, MYSQL* pSql);

// getword: read the next word from string of characters
// Parameters:
//		char** word - resulted word (dinamically allocated); must be deallocated with
// free(*word)
//		const char** line - pointer to string of characters; incremented after reading word
//		char stop - stop character (word separator)
void getword(char** word, const char** line, char stop);

// SetReaderAccess: update current context: set reader access to publication sections
// according to user subscriptions.
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
void SetReaderAccess(TOLContext& c, MYSQL* pSql);

// Search: perform search action; search against the database for keywords retrieved from
// cgi environment
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		CGI& cgi - cgi environment
int Search(TOLContext& c, MYSQL* pSql, CGI& cgi);

// ParseKeywords: read keywords from a string of keywords and add them to current context
// Parameters:
//		const char* s - string of keywords
//		TOLContext& c - current context
void ParseKeywords(const char* s, TOLContext& c);

// IsSeparator: return true if c character is separator
// Parameters:
//		char c - character to test
bool IsSeparator(char c);

// RunParser:
//   - prepare the context: read cgi environment into context, read user subscriptions
//     into context
//   - perform requested actions: log user in, add user to database, add subscriptions,
//     modify user informations, search keywords against database
//   - launch parser with current context: search for a parser instance of the requested
//     template; if not found, create parser instance for requested template and add it
//     to parsers hash; call Parse and WriteOutput methods of parser instance; eventually
//     call PrintParseErrors and PrintWriteErrors for admin users
// Return RES_OK if no errors occured, error code otherwise
// Parameters:
//		MYSQL* p_pSql - pointer to MySQL connection
//		CGIParams* p_pParams - pointer to cgi environment structure
//		fstream& p_rOs - output stream
int RunParser(MYSQL* p_pSQL, CGIParams* p_pParams, fstream& p_rOs) throw(Exception);

// NextParam: read next parameter from string of parameters; return pointer to parameter
// Read parameter is dynamically allocated and it must be deallocated using delete operator.
// Parameters:
//		cpChar p_pchParams - string of parameters
//		int* p_pnIndex - current index in the string
//		int p_nMax - string length
pChar NextParam(cpChar p_pchParams, int* p_pnIndex, int p_nMax) throw(Exception)
{
	if (p_pchParams == NULL)
		throw Exception("Invalid params");
	if (*p_pnIndex >= p_nMax)
		throw Exception("Mising parameter");
	int nIndexNext = *p_pnIndex + strlen(p_pchParams + *p_pnIndex) + 1;
	pChar pchParam = new char[nIndexNext - *p_pnIndex];
	if (pchParam == NULL)
		throw Exception("Alloc error");
	strcpy(pchParam, p_pchParams + *p_pnIndex);
	*p_pnIndex = nIndexNext;
	return pchParam;
}

// ReadCGIParams: read cgi environment from string into cgi environment structure
// Parameters:
//		cpChar p_pchParams - string of parameters
CGIParams* ReadCGIParams(cpChar p_pchParams) throw(Exception)
{
	if (p_pchParams == 0)
		throw Exception("NULL Params");
	CGIParams* pParams = new CGIParams;
	if (pParams == 0)
		throw Exception("Can not alloc memory");
	const int* pnSize = (const int*)p_pchParams;
	int nIndex = 4;
	try
	{
		pParams->m_pchDocumentRoot = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchIP = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchPathTranslated = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchPathInfo = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchRequestMethod = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchQueryString = NextParam(p_pchParams, &nIndex, *pnSize);
		try
		{
			pParams->m_pchHttpCookie = NextParam(p_pchParams, &nIndex, *pnSize);
		}
		catch (Exception& rcoEx)
		{
			pParams->m_pchHttpCookie = NULL;
		}
	}
	catch (Exception& rcoEx)
	{
		delete pParams;
		throw rcoEx;
		return NULL;
	}
	return pParams;
}

// MyThreadRoutine: thread routine; this is started on new thread start
// Parameters:
//		void* p_pArg - pointer to connection to client socket
void* MyThreadRoutine(void* p_pArg)
{
	if (p_pArg == 0)
	{
		cout << "MyThreadRoutine: Invalid arg\n";
		return NULL;
	}
	TOLAction::InitTempMembers();
	CTCPSocket* pcoClSock = (CTCPSocket*)p_pArg;
	char pchBuff[4];
	char* pchMsg = 0;
	CGIParams* pParams = NULL;
	struct timeval tVal = { 0, 0 };
	tVal.tv_sec = 5;
	fd_set clSet;
	FD_ZERO(&clSet);
	FD_SET((SOCKET)*pcoClSock, &clSet);
	MYSQL* pSql = NULL;
	try
	{
		if (select(FD_SETSIZE, &clSet, NULL, NULL, &tVal) == -1
		        || !FD_ISSET((SOCKET)*pcoClSock, &clSet))
		{
			throw Exception("Error on select");
		}
		if (pcoClSock->Recv(pchBuff, 4) < 4)
			throw Exception("Error receiving packet");
		int* pnMsgLen = (int*)pchBuff;
		pchMsg = new char[*pnMsgLen + 1];
		if (pchMsg == 0)
			throw Exception("Out of memory");
		int nCnt = 0;
		while (nCnt < *pnMsgLen)
		{
			if (select(FD_SETSIZE, &clSet, NULL, NULL, &tVal) == -1
			        || !FD_ISSET((SOCKET)*pcoClSock, &clSet))
			{
				throw Exception("Error on select");
			}
			nCnt += pcoClSock->Recv(pchMsg + nCnt, *pnMsgLen - nCnt);
		}
		pchMsg[*pnMsgLen] = 0;
		pParams = ReadCGIParams(pchMsg);
		fstream coOs((SOCKET)*pcoClSock);
		pSql = MYSQLConnection();
		if (pSql == NULL)		// unable to connect to server
		{
			coOs << "<html><head><title>REQUEST ERROR</title></head>\n"
			"<body>Unable to connect to database server.</body></html>\n";
		}
		else
		{
			RunParser(pSql, pParams, coOs);
		}
		coOs.flush();
		delete pParams;
		delete pchMsg;
		pcoClSock->Shutdown();
		delete pcoClSock;
	}
	catch (Exception& coEx)
	{
		delete pParams;
		delete pchMsg;
		pcoClSock->Shutdown();
		delete pcoClSock;
#ifdef _DEBUG
		cout << "MyThreadRoutine: " << coEx.Message() << endl;
#endif
	}
	catch (SocketErrorException& coEx)
	{
		delete pParams;
		delete pchMsg;
		pcoClSock->Shutdown();
		delete pcoClSock;
#ifdef _DEBUG
		cout << "MyThreadRoutine: " << coEx.Message() << endl;
#endif
	}
	return NULL;
}

// nMainThreadPid: pid of main thread
int nMainThreadPid;

// SigHandler: TERM signal handler
void SigHandler(int p_nSig)
{
	if (nMainThreadPid != 0)
	{
		kill(nMainThreadPid, SIGTERM);
		nMainThreadPid = 0;
	}
	exit(0);
}

// StartWatchDog: start watch dog
// Parameters:
//		bool p_bRunAsDaemon - if true, detach and run in background
void StartWatchDog(bool p_bRunAsDaemon)
{
	if (p_bRunAsDaemon)
	{
		if (fork() != 0)
			exit(0);
		setsid();
	}
	while (1)
	{
		nMainThreadPid = fork();
		if (nMainThreadPid != 0)
		{
			signal(SIGTERM, SigHandler);
			waitpid(nMainThreadPid, NULL, 0);
			sleep(10);
			continue;
		}
		if (nMainThreadPid == 0)
			return ;
		if (nMainThreadPid < 0)
		{
			sleep(10);
		}
	}
}

// ProcessArgs: process command line arguments
//		int argc - arguments number
//		char** argv - arguments list
//		bool& p_rbRunAsDaemon - set by this function according to arguments
//		int& p_rnMaxThreads - set by this function according to arguments
void ProcessArgs(int argc, char** argv, bool& p_rbRunAsDaemon, int& p_rnMaxThreads)
{
	if (argc < 2)
		return ;
	for (int i = 1; i < argc; i++)
	{
		if (strcmp(argv[i], "-d") == 0)
			p_rbRunAsDaemon = false;
		if (strcmp(argv[i], "-t") == 0)
		{
			if (++i < argc)
			{
				int nReqMaxThreads = atoi(argv[i]);
				if (nReqMaxThreads < 1)
				{
					cout << "Number of maximum threads must be at least 1. Running with "
					<< p_rnMaxThreads << endl;
				}
				else
					p_rnMaxThreads = nReqMaxThreads;
			}
			else
			{
				cout << "You did not specify the number of maximum threads. Running with "
				<< p_rnMaxThreads << endl;
				break;
			}
		}
		if (strcmp(argv[i], "-h") == 0)
		{
			cout << "Usage: tol_server [-d|-t <threads_nr>|-h]\n"
			"where:\t-d: run in console (by default run as daemon)\n"
			"\t-t <threads_nr>: set the maximum number of threads to start "
			"(default: " << p_rnMaxThreads << ")\n"
			"\t-h: print this help message\n";
		}
	}
}

// ReadConf: read configuration from conf file
// Return 0 if no error encountered
// Parameters:
//		int& p_rnMaxThreads - set by this function according to arguments
int ReadConf(int& p_rnMaxThreads)
{
	fstream coConfFile(CONF_FILE, ios::in);
	if (!coConfFile.is_open())
		return 1;
	while (!coConfFile.eof())
	{
		string coWord;
		cin >> coWord;
		if (coWord == "THREADS")
		{
			int nMaxThreads;
			cin >> nMaxThreads;
			if (nMaxThreads < 1)
			{
				p_rnMaxThreads = nMaxThreads;
				return 1;
			}
			return 0;
		}
	}
	return 1;
}

// main: main function
// Return 0 if no error encountered; error code otherwise
// Parameters:
//		int argc - arguments number
//		char** argv - arguments list
int main(int argc, char** argv)
{
	nMainThreadPid = 0;
	bool bRunAsDaemon = true;
	int nMaxThreads = MAX_THREADS;
	ReadConf(nMaxThreads);
	ProcessArgs(argc, argv, bRunAsDaemon, nMaxThreads);
	StartWatchDog(bRunAsDaemon);
	signal(SIGTERM, SIG_DFL);
	try
	{
		CServerSocket coServer("0.0.0.0", TOL_SRV_PORT);
		ThreadPool coThreadPool(1, nMaxThreads, MyThreadRoutine, NULL);
		CTCPSocket* pcoClSock = NULL;
		for (; ; )
		{
			try
			{
				pcoClSock = coServer.Accept();
				if (pcoClSock == 0)
					throw SocketErrorException("Accept error");
				coThreadPool.StartThread(true, (void*)pcoClSock);
			}
			catch (ExThread& coEx)
			{
				pcoClSock->Shutdown();
				delete pcoClSock;
#ifdef _DEBUG
				cout << coEx.Message() << endl;
#endif
			}
			catch (SocketErrorException& coEx)
			{
				pcoClSock->Shutdown();
				delete pcoClSock;
#ifdef _DEBUG
				cout << coEx.Message() << endl;
#endif
			}
		}
	}
	catch (ExMutex& rcoEx)
	{
		cout << rcoEx.Message() << endl;
		return 1;
	}
	catch (ExThread& rcoEx)
	{
		cout << "Thread: " << rcoEx.ThreadId() << ", Severity: " << rcoEx.Severity()
		<< "; " << rcoEx.Message() << endl;
		return 2;
	}
	catch (SocketErrorException& rcoEx)
	{
		cout << rcoEx.Message() << endl;
		return 3;
	}
	return 0;
}

// RunParser:
//   - prepare the context: read cgi environment into context, read user subscriptions
//     into context
//   - perform requested actions: log user in, add user to database, add subscriptions,
//     modify user informations, search keywords against database
//   - launch parser with current context: search for a parser instance of the requested
//     template; if not found, create parser instance for requested template and add it
//     to parsers hash; call Parse and WriteOutput methods of parser instance; eventually
//     call PrintParseErrors and PrintWriteErrors for admin users
// Return RES_OK if no errors occured, error code otherwise
// Parameters:
//		MYSQL* p_pSql - pointer to MySQL connection
//		CGIParams* p_pParams - pointer to cgi environment structure
//		fstream& p_rOs - output stream
int RunParser(MYSQL* p_pSQL, CGIParams* p_pParams, fstream& p_rOs) throw(Exception)
{
	if (p_pParams == NULL)
		throw Exception("Invalid params");
	if (p_pSQL == NULL)
		throw Exception("MYSQL connection not initialised");
	static const char* ppchParams[PARAM_NR] =
	    {
	        "Name", "EMail", "CountryCode", "UName", "Password", "PasswordAgain",
	        "City", "StrAddress", "State", "Phone", "Fax", "Contact", "Phone2",
	        "Title", "Gender", "Age", "PostalCode", "Employer", "EmployerType",
	        "Position", "Interests", "How", "Languages", "Improvements", "Pref1",
	        "Pref2", "Pref3", "Pref4", "Field1", "Field2", "Field3", "Field4",
	        "Field5", "Text1", "Text2", "Text3"
	    };
	static const int errs[ERR_NR] =
	    {
	        UERR_NO_NAME, UERR_NO_EMAIL, UERR_NO_COUNTRY, UERR_NO_UNAME,
	        UERR_NO_PASSWORD, UERR_NO_PASSWORD_AGAIN
	    };
	CGI* pcoCgi = new CGI(p_pParams->m_pchRequestMethod, p_pParams->m_pchQueryString);
	TOLContext* pcoCtx = new TOLContext;
	if (pcoCgi == NULL || pcoCtx == NULL)
	{
		delete pcoCgi;
		delete pcoCtx;
		throw Exception("Alloc error");
		return -1;
	}
	const char* pchStr;
	bool bDebug = false, bPreview = false, bTechDebug = false;
	char pchBuf[300];
	pcoCtx->SetIP(htonl(inet_addr(p_pParams->m_pchIP)));
	if ((pchStr = pcoCgi->GetFirst(P_IDLANG)) != NULL)
	{
		pcoCtx->SetLanguage(atol(pchStr));
		pcoCtx->SetDefLanguage(atol(pchStr));
	}
	if ((pchStr = pcoCgi->GetFirst(P_IDPUBL)) != NULL)
	{
		pcoCtx->SetPublication(atol(pchStr));
		pcoCtx->SetDefPublication(atol(pchStr));
	}
	if ((pchStr = pcoCgi->GetFirst(P_NRISSUE)) != NULL)
	{
		pcoCtx->SetIssue(atol(pchStr));
		pcoCtx->SetDefIssue(atol(pchStr));
	}
	if ((pchStr = pcoCgi->GetFirst(P_NRSECTION)) != NULL)
	{
		pcoCtx->SetSection(atol(pchStr));
		pcoCtx->SetDefSection(atol(pchStr));
	}
	if ((pchStr = pcoCgi->GetFirst(P_NRARTICLE)) != NULL)
	{
		pcoCtx->SetArticle(atol(pchStr));
		pcoCtx->SetDefArticle(atol(pchStr));
	}
	if ((pchStr = pcoCgi->GetFirst(P_ILSTART)) != NULL)
		pcoCtx->SetIListStart(atol(pchStr));
	if ((pchStr = pcoCgi->GetFirst(P_SLSTART)) != NULL)
		pcoCtx->SetSListStart(atol(pchStr));
	if ((pchStr = pcoCgi->GetFirst(P_ALSTART)) != NULL)
		pcoCtx->SetAListStart(atol(pchStr));
	if ((pchStr = pcoCgi->GetFirst(P_SRLSTART)) != NULL)
		pcoCtx->SetSrListStart(atol(pchStr));
	if ((pchStr = pcoCgi->GetFirst("ST_max")) != NULL)
	{
		int st_max = atol(pchStr);
		for (int i = 1; i <= st_max; i++)
		{
			string coField, coArtType;
			sprintf(pchBuf, "ST%d", i);
			if ((pchStr = pcoCgi->GetFirst(pchBuf)))
				coField = pchStr;
			sprintf(pchBuf, "ST_T%d", i);
			if ((pchStr = pcoCgi->GetFirst(pchBuf)))
				coArtType = pchStr;
			if (coField == "" || coArtType == "")
				continue;
			pcoCtx->SetField(coField, coArtType);
			sprintf(pchBuf, "ST_LS%d", i);
			if ((pchStr = pcoCgi->GetFirst(pchBuf)))
				pcoCtx->SetStListStart(atol(pchStr), coField);
			sprintf(pchBuf, "ST_PS%d", i);
			if ((pchStr = pcoCgi->GetFirst(pchBuf)))
				pcoCtx->SetStartSubtitle(atol(pchStr), coField);
			sprintf(pchBuf, "ST_AS%d", i);
			if ((pchStr = pcoCgi->GetFirst(pchBuf)))
				pcoCtx->SetAllSubtitles(atol(pchStr), coField);
		}
	}
	CheckUserInfo(*pcoCgi, *pcoCtx, ppchParams, PARAM_NR);
	bool bAccessAll = false;
	pcoCtx->SetReader(true);
	if ((pchStr = pcoCgi->GetFirst(P_SEARCH)))
	{
		int nRes = Search(*pcoCtx, p_pSQL, *pcoCgi);
		if (nRes < SRERR_INTERNAL && nRes != 0)
			nRes = SRERR_INTERNAL;
		pcoCtx->SetSearchRes(nRes);
	}
	if ((pchStr = pcoCgi->GetFirst(P_USERADD)))
	{
		int nRes = AddUser((*pcoCtx), p_pSQL, ppchParams, PARAM_NR, errs, ERR_NR);
		if (nRes < UERR_INTERNAL && nRes != 0)
			nRes = UERR_INTERNAL;
		pcoCtx->SetAddUserRes(nRes);
		if (nRes == 0)
			p_rOs << "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"TOL_UserId="
			<< pcoCtx->User() << "; path=/\">\n"
			<< "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"TOL_UserKey="
			<< pcoCtx->Key() << "; path=/\">\n";
	}
	else if ((pchStr = pcoCgi->GetFirst(P_LOGIN)))
	{
		int nRes = Login((*pcoCgi), (*pcoCtx), p_pSQL);
		if (nRes < LERR_INTERNAL && nRes != 0)
			nRes = LERR_INTERNAL;
		pcoCtx->SetLoginRes(nRes);
		if (nRes == 0)
			p_rOs << "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"TOL_UserId="
			<< pcoCtx->User() << "; path=/\">\n"
			<< "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"TOL_UserKey="
			<< pcoCtx->Key() << "; path=/\">\n";
	}
	else if ((pchStr = p_pParams->m_pchHttpCookie) != 0 && *pchStr != 0)
	{
		while (1)
		{
			pChar pchWord, pchValue;
			while (*pchStr <= ' ' && *pchStr != 0) pchStr++;
			getword(&pchWord, &pchStr, '=');
			if (pchWord[0] == 0)
			{
				free(pchWord);
				break;
			}
			getword(&pchValue, &pchStr, ';');
			if (strcmp(pchWord, "TOL_UserId") == 0)
				pcoCtx->SetUser(atol(pchValue));
			else if (strcmp(pchWord, "TOL_UserKey") == 0)
				pcoCtx->SetKey(strtoul(pchValue, 0, 10));
			else if (strcmp(pchWord, "TOL_Access") == 0)
				bAccessAll = strcmp(pchValue, "all") == 0;
			else if (strcmp(pchWord, "TOL_Preview") == 0)
				bPreview = strcmp(pchValue, "on") == 0;
			else if (strcmp(pchWord, "TOL_Debug") == 0)
				bTechDebug = strcmp(pchValue, "on") == 0;
			free(pchValue);
			free(pchWord);
		}
	}
	if ((pchStr = pcoCgi->GetFirst(P_USERMODIFY)))
	{
		int nRes = ModifyUser((*pcoCtx), p_pSQL, ppchParams, PARAM_NR, errs, ERR_NR);
		if (nRes < UERR_INTERNAL && nRes != 0)
			nRes = UERR_INTERNAL;
		pcoCtx->SetModifyUserRes(nRes);
	}
	if ((pchStr = pcoCgi->GetFirst(P_SUBSTYPE)))
	{
		if (strcasecmp(pchStr, "paid") == 0)
			pcoCtx->SetSubsType(ST_PAID);
		if (strcasecmp(pchStr, "trial") == 0)
			pcoCtx->SetSubsType(ST_TRIAL);
	}
	if ((pchStr = pcoCgi->GetFirst(P_SUBSCRIBE)))
	{
		int nRes = DoSubscribe((*pcoCgi), (*pcoCtx), p_pSQL);
		if (nRes < SERR_INTERNAL && nRes != 0)
			nRes = SERR_INTERNAL;
		pcoCtx->SetSubsRes(nRes);
	}
	long int nIdUserIP = -1;
	sprintf(pchBuf, "select IdUser from SubsByIP where StartIP <= %lu and "
	        "%lu <= (StartIP + Addresses - 1)", pcoCtx->IP(), pcoCtx->IP());
	SQLQuery(p_pSQL, pchBuf);
	StoreResult(p_pSQL, coSqlRes);
	if (mysql_num_rows(*coSqlRes) > 0)
	{
		MYSQL_ROW row = mysql_fetch_row(*coSqlRes);
		if (pcoCtx->User() < 0)
			pcoCtx->SetUser(atol(row[0]));
		else
			nIdUserIP = atol(row[0]);
		pcoCtx->SetAccessByIP(true);
	}
	if (pcoCtx->User() >= 0)
	{
		sprintf(pchBuf, "select Reader, KeyId from Users where Id = %ld", pcoCtx->User());
		SQLQuery(p_pSQL, pchBuf);
		StoreResult(p_pSQL, coSqlRes);
		MYSQL_ROW row;
		bool bHasAccess = false;
		if (mysql_num_rows(*coSqlRes) > 0)
		{
			row = mysql_fetch_row(*coSqlRes);
			pcoCtx->SetReader(row[0][0] == 'Y');
			if (row[1] != 0 && pcoCtx->Key() == strtoul(row[1], 0, 10))
			{
				bHasAccess = true;
			}
			else
				pcoCtx->SetKey(0);
		}
		else
			pcoCtx->SetUser( -1);
		if (!bHasAccess && nIdUserIP >= 0)
		{
			sprintf(pchBuf, "select Reader from Users where Id = %ld", nIdUserIP);
			SQLQuery(p_pSQL, pchBuf);
			coSqlRes = mysql_store_result(p_pSQL);
			if (mysql_num_rows(*coSqlRes) > 0)
			{
				row = mysql_fetch_row(*coSqlRes);
				pcoCtx->SetUser(nIdUserIP);
				pcoCtx->SetReader(row[0][0] == 'Y');
				pcoCtx->SetKey(0);
			}
		}
		if (bHasAccess || pcoCtx->AccessByIP())
		{
			if (bAccessAll && !pcoCtx->IsReader())
				pcoCtx->SetAccess(A_ALL);
			if (pcoCtx->IsReader())
			{
				bTechDebug = bDebug = bPreview = false;
				SetReaderAccess((*pcoCtx), p_pSQL);
			}
		}
		else
			bTechDebug = bDebug = bPreview = false;
	}
	try
	{
		TOLParser::SetMYSQL(p_pSQL);
		TOLParser::LockHash();
		TOLParserHash& coPHash = TOLParser::GetHash();
		TOLParserHash::iterator ph_i = coPHash.find(p_pParams->m_pchPathTranslated);
		if (ph_i == coPHash.end())
			coPHash.insert_unique(new TOLParser(p_pParams->m_pchPathTranslated,
			                                    p_pParams->m_pchDocumentRoot));
		ph_i = coPHash.find(p_pParams->m_pchPathTranslated);
		if (ph_i == coPHash.end())
			throw Exception("Parser hash error");
		TOLParser::UnlockHash();
		(*ph_i)->SetDebug(bTechDebug);
		int nParseRes = (*ph_i)->Parse();
		WriteCharset((*pcoCtx), p_pSQL, p_rOs);
		int nWriteRes = (*ph_i)->WriteOutput(*pcoCtx, p_rOs);
		if (bPreview == true)
		{
			p_rOs << "<script LANGUAGE=\"JavaScript\">parent.e.document.open();\n"
			"parent.e.document.write(\"<html><head><title>Errors</title>"
			"</head><body bgcolor=white text=black>\\\n<pre>\\\n";
			if (bTechDebug == true)
			{
				p_rOs << "\\\nPARSE RESULT: " << nParseRes << "\\\n";
			}
			p_rOs << "\\\nParse errors:\\\n";
			(*ph_i)->PrintParseErrors(p_rOs, true);
			if (bTechDebug)
			{
				p_rOs << "\\\nWRITE RESULT:" << nWriteRes << "\\\n";
			}
			p_rOs << "\\\nWrite errors:\\\n";
			(*ph_i)->PrintWriteErrors(p_rOs, true);
			p_rOs << "</pre></body></html>\\\n\");\nparent.e.document.close();\n</script>\n";
		}
		TOLParser::SetMYSQL(NULL);
		delete pcoCgi;
		delete pcoCtx;
	}
	catch (ExStat& rcoEx)
	{
		delete pcoCgi;
		delete pcoCtx;
		throw Exception("Error loading template file");
		return -1;
	}
	catch (Exception& rcoEx)
	{
		delete pcoCgi;
		delete pcoCtx;
		throw rcoEx;
		return -1;
	}
	catch (ExMutex& rcoEx)
	{
		delete pcoCgi;
		delete pcoCtx;
		throw Exception(rcoEx.Message());
		return -1;
	}
	return 0;
}

// WriteCharset: write http tag specifying the charset - according to current language
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		fstream& fs - output stream
int WriteCharset(TOLContext& c, MYSQL* pSql, fstream& fs)
{
	if (c.Language() < 0)
		return -1;
	char pchBuf[100];
	sprintf(pchBuf, "select CodePage from Languages where Id = %ld", c.Language());
	SQLQuery(pSql, pchBuf);
	StoreResult(pSql, coSqlRes);
	CheckForRows(*coSqlRes, 1);
	FetchRow(*coSqlRes, row);
	fs << "<META HTTP-EQUIV=\"charset\" CONTENT=\"" << row[0] << "\">\n";
	return 0;
}

// Login: perform login action: log user in
// Parameters:
//		CGI& cgi - cgi environment
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int Login(CGI& cgi, TOLContext& c, MYSQL* pSql)
{
	c.SetLogin(true);
	const char* s;
	string uname, password, q;
	if ((s = cgi.GetFirst("LoginUName")) == 0 || s[0] == 0)
		return LERR_NO_UNAME;
	uname = s;
	if ((s = cgi.GetFirst("LoginPassword")) == 0 || s[0] == 0)
		return LERR_NO_PASSWORD;
	password = s;
	q = "select Password, password(\"" + password + "\") from Users where UName "
	    "= \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	StoreResult(pSql, coSqlRes);
	if (mysql_num_rows(*coSqlRes) < 1)
	{
		return LERR_INVALID_UNAME;
	}
	FetchRow(*coSqlRes, row);
	if (strcmp(row[0], row[1]))
	{
		return LERR_INVALID_PASSWORD;
	}
	q = "update Users set KeyId = RAND()*1000000000+RAND()*1000000+RAND()*1000 "
	    "where UName = \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	q = "select Id, KeyId from Users where UName = \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	coSqlRes = mysql_store_result(pSql);
	CheckForRows(*coSqlRes, 1);
	row = mysql_fetch_row(*coSqlRes);
	c.SetUser(atol(row[0]));
	c.SetKey(atol(row[1]));
	return 0;
}

// CheckUserInfo: read user informations from CGI parameters
// Parameters:
//		CGI& cgi - cgi environment
//		TOLContext& c - current context
//		const char* ppchParams[] - parameters to read
//		int param_nr - parameters number
int CheckUserInfo(CGI& cgi, TOLContext& c, const char* ppchParams[], int param_nr)
{
	string field_pref = "User";
	int found = 0;
	for (int i = 0; i < param_nr; i++)
	{
		string fld = field_pref + ppchParams[i];
		const char* s = cgi.GetFirst(fld.c_str());
		if (s == 0)
			continue;
		c.SetUserInfo(string(ppchParams[i]), string(s));
		found ++;
	}
	return found;
}

// AddUser: perform add user action (add user to database); return error code
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		const char* ppchParams[] - parameters to read from context (user information)
//		int param_nr - parameters number
//		const int errs[] - error codes
//		int err_nr - errors number
int AddUser(TOLContext& c, MYSQL* pSql, const char* ppchParams[], int param_nr,
            const int errs[], int err_nr)
{
	c.SetAddUser(true);
	string field_pref = "User", q, fn, fv, uname, email, password;
	fn = "KeyId";
	fv = "RAND()*1000000000+RAND()*1000000+RAND()*1000";
	for (int i = 0; i < param_nr; i++)
	{
		string s = c.UserInfo(string(ppchParams[i]));
		char pchBuf[10000];
		mysql_escape_string(pchBuf, s.c_str(), strlen(s.c_str()));
		if (i < err_nr && s == "") return errs[i];
		if (s == "") continue;
		if (i == 4)
		{
			password = pchBuf;
			continue;
		}
		if (i == 1) email = s;
		if (i == 3) uname = s;
		fn += string(", ") + (i == 5 ? ppchParams[i - 1] : ppchParams[i]);
		if (i == 5)
		{
			if (password != pchBuf)
				return UERR_PASSWORDS_DONT_MATCH;
			if (strlen(s.c_str()) < 6)
				return UERR_PASSWORD_TOO_SIMPLE;
			fv += string(", password(\"") + pchBuf + "\")";
		}
		else if (strncasecmp(ppchParams[i], "Pref", 4))
			fv += string(", \"") + pchBuf + "\"";
		else
		{
			if (s == "on")
				fv += ", \"Y\"";
			else
				fv += ", \"N\"";
		}
	}
	q = "select * from Users where UName = \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	StoreResult(pSql, coSqlRes);
	if (mysql_num_rows(*coSqlRes) > 0)
		return UERR_USER_EXISTS;
	q = "select * from Users where EMail = \"" + email + "\"";
	SQLQuery(pSql, q.c_str());
	coSqlRes = mysql_store_result(pSql);
	if (mysql_num_rows(*coSqlRes) > 0)
		return UERR_DUPLICATE_EMAIL;
	q = "insert into Users (" + fn + ") values(" + fv + ")";
	SQLQuery(pSql, q.c_str());
	q = "select Id, KeyId from Users where UName = \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	coSqlRes = mysql_store_result(pSql);
	CheckForRows(*coSqlRes, 1);
	FetchRow(*coSqlRes, row);
	c.SetUser(atol(row[0]));
	c.SetKey(atol(row[1]));
	return 0;
}

// ModifyUser: perform modify user action (modify user information in the database)
// Return error code.
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		const char* ppchParams[] - parameters to read from context (user information)
//		int param_nr - parameters number
//		const int errs[] - error list (errors codes)
//		int err_nr - errors number
int ModifyUser(TOLContext& c, MYSQL* pSql, const char* ppchParams[], int param_nr,
               const int errs[], int err_nr)
{
	char pchBuf[10000];
	c.SetModifyUser(true);
	string field_pref = "User", q, f, password;
	bool first = true;
	for (int i = 0; i < param_nr; i++)
	{
		if (!c.IsUserInfo(string(ppchParams[i])) || i == 3)
			continue;
		string s = c.UserInfo(string(ppchParams[i]));
		int slen = strlen(s.c_str()) > 5000 ? 5000 : strlen(s.c_str());
		mysql_escape_string(pchBuf, s.c_str(), slen);
		if (i < err_nr && s == "") return errs[i];
		if (s == "") continue;
		if (i == 4)
		{
			password = pchBuf;
			continue;
		}
		if (!first) f += ", ";
		f += string((i == 5 ? ppchParams[i - 1] : ppchParams[i])) + " = ";
		if (i == 5)
		{
			if (password != pchBuf)
				return UERR_PASSWORDS_DONT_MATCH;
			if (strlen(s.c_str()) < 6)
				return UERR_PASSWORD_TOO_SIMPLE;
			f += string("password(\"") + pchBuf + "\")";
		}
		else if (strncasecmp(ppchParams[i], "Pref", 4))
			f += string("\"") + pchBuf + "\"";
		else
			f += string("\"") + (s == "on" ? "Y" : "N") + "\"";
		if (first) first = false;
	}
	sprintf(pchBuf, "%ld", c.User());
	q = string("select * from Users where Id = \"") + pchBuf + "\"";
	SQLQuery(pSql, q.c_str());
	StoreResult(pSql, coSqlRes);
	if (mysql_num_rows(*coSqlRes) <= 0)
		return UERR_INVALID_USER;
	q = string("update Users set ") + f + " where Id = \"" + pchBuf + "\"";
	SQLQuery(pSql, q.c_str());
	return 0;
}

// DoSubscribe: perform subscribe action (subscribe user to a certain publication)
// Parameters:
//		CGI& cgi - cgi environment
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int DoSubscribe(CGI& cgi, TOLContext& c, MYSQL* pSql)
{
	if (c.SubsType() == ST_NONE)
		return SERR_TYPE_NOT_SPECIFIED;
	c.SetSubscribe(true);
	char pchBuf[2000];
	if (c.User() < 0)
		return SERR_USER_NOT_SPECIFIED;
	if (!c.IsReader())
		return SERR_USER_NOT_READER;
	if (c.Publication() < 0)
		return SERR_PUBL_NOT_SPECIFIED;
	const char* s;
	sprintf(pchBuf, "select TimeUnit, PayTime, UnitCost, Currency from Publications where "
	        "Id = %ld", c.Publication());
	SQLQuery(pSql, pchBuf);
	StoreResult(pSql, coSqlRes);
	CheckForRows(*coSqlRes, 1);
	FetchRow(*coSqlRes, row);
	cpChar modifier = "";
	if (row[0][0] == 'D')
		modifier = "DAY";
	else if (row[0][0] == 'W')
		modifier = "WEEK";
	else if (row[0][0] == 'M')
		modifier = "MONTH";
	else if (row[0][0] == 'Y')
		modifier = "YEAR";
	else
		return SERR_UNIT_NOT_SPECIFIED;
	long int paid_time = atol(row[1]);
	double unit_cost = atof(row[2]);
	string currency = row[3];
	long int id_subscription;
	bool active = true;
	my_ulonglong rows = 0;
	sprintf(pchBuf, "select Id, Active, ToPay from Subscriptions where IdUser = %ld and "
	        "IdPublication = %ld", c.User(), c.Publication());
	SQLQuery(pSql, pchBuf);
	coSqlRes = mysql_store_result(pSql);
	char subs_type = c.SubsType() == ST_TRIAL ? 'T' : 'P';
	if (mysql_num_rows(*coSqlRes) > 0)
	{
		row = mysql_fetch_row(*coSqlRes);
		id_subscription = atol(row[0]);
		active = row[1][0] == 'Y';
		if (atof(row[2]) > 0)
			return SERR_SUBS_NOT_PAID;
		if (!active)
		{
			sprintf(pchBuf, "update Subscriptions set Active = 'Y', Type = '%c' were IdUser"
			        " = %ld and IdPublication = %ld", subs_type, c.User(), c.Publication());
			SQLQuery(pSql, pchBuf);
		}
	}
	else
	{
		sprintf(pchBuf, "insert into Subscriptions (IdUser, IdPublication, Active, Type) "
		        "values(%ld, %ld, 'Y', '%c')", c.User(), c.Publication(), subs_type);
		SQLQuery(pSql, pchBuf);
		sprintf(pchBuf, "select Id from Subscriptions where IdUser = %ld and "
		        "IdPublication = %ld", c.User(), c.Publication());
		SQLQuery(pSql, pchBuf);
		coSqlRes = mysql_store_result(pSql);
		CheckForRows(*coSqlRes, 1);
		row = mysql_fetch_row(*coSqlRes);
		id_subscription = atol(row[0]);
	}
	bool by_publication = false;
	double to_pay = 0;
	if ((s = cgi.GetFirst("by")) != 0 && strcasecmp(s, "publication") == 0)
	{
		by_publication = true;
		sprintf(pchBuf, "select Number from Issues where IdPublication = %ld and "
		        "Published = 'Y' and IdLanguage = %ld order by Number DESC limit 0, 1",
		        c.Publication(), c.Language());
		SQLQuery(pSql, pchBuf);
		coSqlRes = mysql_store_result(pSql);
		CheckForRows(*coSqlRes, 1);
		row = mysql_fetch_row(*coSqlRes);
		c.SetIssue(atol(row[0]));
		cpChar sel_time = c.SubsType() == ST_TRIAL ? "TrialTime" : "PaidTime";
		sprintf(pchBuf, "select TO_DAYS(ADDDATE(now(), INTERVAL %s %s)) - TO_DAYS(now()), %s "
		        "from SubsDefTime, Users where IdPublication = %ld and "
		        "SubsDefTime.CountryCode = Users.CountryCode and Users.Id = %ld",
		        sel_time, modifier, sel_time, c.Publication(), c.User());
		SQLQuery(pSql, pchBuf);
		coSqlRes = mysql_store_result(pSql);
		CheckForRows(*coSqlRes, 1);
		row = mysql_fetch_row(*coSqlRes);
		long int subs_days = atol(row[0]);
		long int time_units = atol(row[1]);
		if (c.SubsType() == ST_TRIAL)
			paid_time = subs_days;
		sprintf(pchBuf, "select Number from Sections where IdPublication = %ld and NrIssue "
		        "= %ld and IdLanguage = %ld", c.Publication(), c.Issue(), c.Language());
		SQLQuery(pSql, pchBuf);
		coSqlRes = mysql_store_result(pSql);
		CheckForRows(*coSqlRes, 1);
		while ((row = mysql_fetch_row(*coSqlRes)) != NULL)
		{
			sprintf(pchBuf, "replace into SubsSections set IdSubscription = %ld, "
			        "SectionNumber = %s, StartDate = now(), Days = %ld, PaidDays = %ld",
			         id_subscription, row[0], subs_days, paid_time);
			SQLQuery(pSql, pchBuf);
			to_pay += unit_cost * time_units;
		}
	}
	if ((s = cgi.GetFirst(P_CB_SUBS)) && !by_publication)
		while (s)
		{
			long int section = atol(s);
			sprintf(pchBuf, "%s%s", P_TX_SUBS, s);
			if ((s = cgi.GetFirst(pchBuf)) == 0)
			{
				s = cgi.GetNext(P_CB_SUBS);
				continue;
			}
			long int time_units = atol(s);
			sprintf(pchBuf, "select TO_DAYS(ADDDATE(now(), INTERVAL %ld %s)) - TO_DAYS(now())",
			        time_units, modifier);
			SQLQuery(pSql, pchBuf);
			coSqlRes = mysql_store_result(pSql);
			CheckForRows(*coSqlRes, 1);
			row = mysql_fetch_row(*coSqlRes);
			long int req_days = atol(row[0]);
			if (c.SubsType() == ST_TRIAL)
				paid_time = req_days;
			sprintf(pchBuf, "select TO_DAYS(ADDDATE(StartDate, INTERVAL Days DAY)) - "
			        "TO_DAYS(now()) from SubsSections where IdSubscription = %ld and "
			        "SectionNumber = %ld", id_subscription, section);
			SQLQuery(pSql, pchBuf);
			coSqlRes = mysql_store_result(pSql);
			to_pay += unit_cost * time_units;
			if ((rows = mysql_num_rows(*coSqlRes)))
			{
				row = mysql_fetch_row(*coSqlRes);
				if (c.SubsType() == ST_TRIAL)
					paid_time = req_days;
				if (atol(row[0]) > 0)
					sprintf(pchBuf, "update SubsSections set Days = Days + %ld, PaidDays = "
					        "PaidDays + %ld where IdSubscription = %ld and SectionNumber = %ld",
					        req_days, paid_time, id_subscription, section);
				else
					sprintf(pchBuf, "update SubsSections set StartDate = now(), Days = %ld, "
					        "PaidDays = %ld where IdSubscription = %ld and SectionNumber = %ld",
					        req_days, paid_time, id_subscription, section);
			}
			else
				sprintf(pchBuf, "insert into SubsSections (IdSubscription, SectionNumber, "
				        "StartDate, Days, PaidDays) values(%ld, %ld, now(), %ld, %ld)",
				        id_subscription, section, req_days, paid_time);
			SQLQuery(pSql, pchBuf);
			s = cgi.GetNext(P_CB_SUBS);
		}
	if (c.SubsType() != ST_TRIAL)
	{
		sprintf(pchBuf, "update Subscriptions set ToPay = ToPay + %f, Currency = '%s' "
		        "where Id = %ld", to_pay, currency.c_str(), id_subscription);
		SQLQuery(pSql, pchBuf);
	}
	return 0;
}

// getword: read the next word from string of characters
// Parameters:
//		char** word - resulted word (dinamically allocated); must be deallocated with
// free(*word)
//		const char** line - pointer to string of characters; incremented after reading word
//		char stop - stop character (word separator)
void getword(char** word, const char** line, char stop)
{
	int x;
	for (x = 0; ((*line && (*line)[x]) && ((*line)[x] != stop)); x++);
	*word = (char*)malloc(x + 1);
	if (*line)
		strncpy(*word, *line, x);
	(*word)[x] = 0;
	if ((*line)[x]) (*line) ++;
	*line += x;
}

// SetReaderAccess: update current context: set reader access to publication sections
// according to user subscriptions.
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
void SetReaderAccess(TOLContext& c, MYSQL* pSql)
{
	if (pSql == 0)
		return ;
	char pchBuf[300];
	sprintf(pchBuf, "select IdPublication, Id from Subscriptions where IdUser = %ld"
	        " and Active = \"Y\"", c.User());
	if (mysql_query(pSql, pchBuf))
		return ;
	MYSQL_RES* pSqlRes = mysql_store_result(pSql);
	if (pSqlRes == 0)
		return ;
	MYSQL_ROW row;
	while ((row = mysql_fetch_row(pSqlRes)))
	{
		long int id_publ = atol(row[0]), id_subs = atol(row[1]);
		sprintf(pchBuf, "select SectionNumber, (TO_DAYS(now())-TO_DAYS(StartDate)), "
		        "PaidDays from SubsSections where IdSubscription = %ld", id_subs);
		if (mysql_query(pSql, pchBuf))
			continue;
		MYSQL_RES *res2 = mysql_store_result(pSql);
		if (res2 == 0)
			continue;
		MYSQL_ROW row2;
		while ((row2 = mysql_fetch_row(res2)))
		{
			long int nr_section = atol(row2[0]);
			long int passed_days = atol(row2[1]);
			long int days = atol(row2[2]);
			if (passed_days <= days)
				c.SetSubs(id_publ, nr_section);
		}
		mysql_free_result(res2);
	}
	mysql_free_result(pSqlRes);
}

// Search: perform search action; search against the database for keywords retrieved from
// cgi environment
// Parameters:
//		TOLContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		CGI& cgi - cgi environment
int Search(TOLContext& c, MYSQL* pSql, CGI& cgi)
{
	c.SetSearch(true);
	cpChar s;
	if ((s = cgi.GetFirst("SearchKeywords")) == 0)
		return SRERR_NO_KEYWORDS;
	ParseKeywords(s, c);
	c.SetStrKeywords(s);
	if ((s = cgi.GetFirst("SearchMode")) && strcasecmp(s, "on") == 0)
		c.SetSearchAnd(true);
	int level = 0;
	if ((s = cgi.GetFirst("SearchLevel")))
		level = atol(s);
	if (level < 0 || level > 2)
		return SRERR_INVALID_LEVEL;
	c.SetSearchLevel(level);
	return 0;
}

// ParseKeywords: read keywords from a string of keywords and add them to current context
// Parameters:
//		const char* s - string of keywords
//		TOLContext& c - current context
void ParseKeywords(const char* s, TOLContext& c)
{
	// " \t\n\r,./\\<>?:;\"'{}[]~`!%^&*()+=\\|"
	cpChar p, q;
	char tmp[256];
	int l;
	if (s)
	{
		for (q = s; *q; )
		{
			p = q;
			while (*q && !IsSeparator(*q))
				q++;
			l = q - p;
			if (l > 1)
			{
				strncpy(tmp, p, (l > 255 ? 255 : l));
				tmp[(l > 255 ? 255 : l)] = 0;
				c.SetKeyword(tmp);
			}
			else
			{
				while (*q && IsSeparator(*q))
					q++;
			}
		}
	}
}

// IsSeparator: return true if c character is separator
// Parameters:
//		char c - character to test
bool IsSeparator(char c)
{
	static const char separators[] = " \t\n\r,./\\<>?:;\"'{}[]~`!%^&*()+=\\|";
	for (int i = 0; separators[i]; i++)
		if (c == separators[i])
			return true;
	return false;
}

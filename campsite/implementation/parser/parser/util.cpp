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

Implementation of general purpose functions

******************************************************************************/

#include <sys/types.h>
#include <fcntl.h>
#include <stdio.h>
#include <sys/stat.h>
#include <unistd.h>
#include <sys/mman.h>
#include <string>
#include <mysql/mysql.h>

#include "util.h"
#include "atoms_impl.h"
#include "cms_types.h"
#include "attributes.h"
#include "error.h"
#include "auto_ptr.h"

string SQL_SERVER;
string SQL_USER;
string SQL_PASSWORD;
string SQL_DATABASE;
int SQL_SRV_PORT = 0;

// SQLConnection: initialise connection to MySQL server
// Parameters: none
// Returns: pointer to MYSQL structure; NULL if error
MYSQL* MYSQLConnection()
{
	// Each thread should use at most on database connection; this is accomplished
	// using a static key variable; this is initialised to NULL at program start
	static TK_MYSQL coMySql(NULL);
	// connect to mysql server (if not already connected)
	if (&coMySql != NULL)
		return &coMySql; 					// already connected
	MYSQL* pSQL = NULL;
	if ((pSQL = mysql_init(pSQL)) == NULL)	// initialise connection to MySQL server
		return NULL;
	pSQL = mysql_real_connect(pSQL, SQL_SERVER.c_str(), SQL_USER.c_str(), SQL_PASSWORD.c_str(),
	                          SQL_DATABASE.c_str(), SQL_SRV_PORT, 0, 0);	// connecto to MySQL server
	if (pSQL == NULL)						// unable to connect
		return NULL;
	coMySql = pSQL;							// set key variable value to MySQL connection
	return pSQL;
}

// GetTypeAttributes: initialise the CTypeAttributesMap container;
//		this will contain all article specific attributes
// Parameters: CTypeAttributesMap** ahash - pointer to pointer to
//		attributes hash; ahash and *ahash must not be NULL
// Returns: RES_OK (0) on success
int GetArticleTypeAttributes(CTypeAttributesMap** ta_h) throw(bad_alloc)
{
	// we need a connection to database server
	MYSQL* sql = MYSQLConnection();
	if (sql == NULL)
		return ERR_SQLCONNECT;

	// type-attributes map
	SafeAutoPtr<CTypeAttributesMap> pcoTypes(new CTypeAttributesMap);

	// query for all table names in the database
	string q = "show tables";
	if (mysql_query(sql, q.c_str()))
		return ERR_QUERY;
	StoreResult(sql, res);
	CheckForRows(*res, 1);
	MYSQL_ROW row;
	while ((row = mysql_fetch_row(*res)))
	{
		if (row[0][0] != 'X')			// select only those tables whose name start with X
			continue;

		// these tables contain the fields of every type of article
		q = string("desc ") + row[0];	// query for table description (fields)
		if (mysql_query(sql, q.c_str()) != 0)
			continue;

		MYSQL_RES* tres = mysql_store_result(sql);
		if (tres == NULL)
			continue;

		CTypeAttributes* pcoType = new CTypeAttributes(row[0] + 1);		// new type
		CStatementContext* pcoCtxList = new CStatementContext(CMS_CT_LIST);
		CStatementContext* pcoCtxPrint = new CStatementContext(CMS_CT_PRINT);
		CStatementContext* pcoCtxWith = new CStatementContext(CMS_CT_WITH);

		MYSQL_ROW trow;
		while ((trow = mysql_fetch_row(tres)))
		{
			if (trow[0][0] != 'F')		// select only those fields whose name start with F
				continue;

			CAttribute* pcoAttr = NULL;		// pointer to new attribute
			if (strncmp(trow[0], "FSW", 3) == 0)			// field type is switch
				pcoAttr = new CSwitchAttr(trow[0] + 3, trow[0]);
			else if (strncmp(trow[1], "int", 3) == 0)		// field type is integer
				pcoAttr = new CIntegerAttr(trow[0] + 1, trow[0]);
			else if (strncmp(trow[1], "datetime", 8) == 0)	// field type is datetime
				pcoAttr = new CDateTimeAttr(trow[0] + 1, trow[0]);
			else if (strncmp(trow[1], "date", 4) == 0)		// field type is date
				pcoAttr = new CDateAttr(trow[0] + 1, trow[0]);
			else if (strncmp(trow[1], "time", 4) == 0)		// field type is time
				pcoAttr = new CTimeAttr(trow[0] + 1, trow[0]);
			else											// others are considered string
				pcoAttr = new CStringAttr(trow[0] + 1, trow[0]);

			pcoCtxList->insertAttr(pcoAttr);
			pcoCtxPrint->insertAttr((CAttribute*)pcoAttr->clone());
			pcoCtxWith->insertAttr((CAttribute*)pcoAttr->clone());
		}
		pcoType->insertCtx(pcoCtxList);
		pcoType->insertCtx(pcoCtxPrint);
		pcoType->insertCtx(pcoCtxWith);
		// insert a new type into type-attributes map
		// the type name is the table name having the X removed
		pcoTypes->operator [](pcoType->name()) = pcoType;
		mysql_free_result(tres);
	}
	*ta_h = pcoTypes.release();
	return RES_OK;
}

// IsValidType: return 0 if the string is the name of a valid article type
// Parameters: const char* type - pointer to const char; article type name; must not be NULL
// Returns: 0 if type is valid; error code otherwise
int IsValidType(const char* t, MYSQL* sql)
{
	SQLQuery(sql, "show tables");
	StoreResult(sql, res);
	CheckForRows(*res, 1);
	MYSQL_ROW row;
	for (row = mysql_fetch_row(*res); row != NULL; row = mysql_fetch_row(*res))
		if (row[0][0] == 'X' && strcmp(t, row[0] + 1) == 0)
		{
			return 0;
		}
	return 1;
}

static char url_ok_chars[] = "*-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz";
static char hex_digits[17] = "0123456789ABCDEF";

// EscapeURL: return the given character string escaped for URL
// Parameters: const char* src - pointer to const char; string to escape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
const char* const EscapeURL(const char* src)
{
	// translates all the charactes that ar not in url_ok_chars string into %xx string
	// %xx specifies the character ascii code in hexadecimal
	if (src == NULL)
		return NULL;
	char* dst = (char*)new char[strlen(src) * 3 + 1];
	if (dst == NULL)
		return NULL;
	int srcI;
	int dstI;
	for (srcI = 0, dstI = 0; src[srcI] != 0; srcI = srcI + 1, dstI = dstI + 1)
		if (src[srcI] == ' ')
			dst[dstI] = '+';
		else if (strchr(url_ok_chars, src[srcI]))
			dst[dstI] = src[srcI];
		else
		{
			dst[(dstI)++] = '%';
			dst[(dstI)++] = hex_digits[((unsigned char) src[srcI]) >> 4];
			dst[dstI] = hex_digits[src[srcI] & 0x0F];
		}
	dst[dstI] = 0;
	return dst;
}

// EscapeURL: return the given character string escaped for HTML
// Parameters: const char* src - pointer to const char; string to escape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
const char* const EscapeHTML(const char *src)
{
	// translates <, >, ", &, end of line and line feed characters into their HTML escapes
	if (src == NULL)
		return NULL;
	char* dst = (char*)new char[strlen(src) * 6 + 1];
	if (dst == NULL)
		return NULL;
	int srcI;
	int dstI;
	for (srcI = 0, dstI = 0; src[srcI] != 0; srcI = srcI + 1, dstI = dstI + 1)
		switch (src[srcI])
		{
		case '<':
			strcpy((dst + dstI), "&lt;");
			dstI = dstI + 3;
			break;
		case '>':
			strcpy((dst + dstI), "&gt;");
			dstI = dstI + 3;
			break;
		case '"':
			strcpy((dst + dstI), "&quot;");
			dstI = dstI + 5;
			break;
		case '&':
			strcpy((dst + dstI), "&amp;");
			dstI = dstI + 4;
			break;
		case '\n':
			if (dstI > 0 && (dst)[dstI - 1] == '\r')
			{
				break;
			}
		case '\r':
			strcpy((dst + dstI), "<BR>\r");
			dstI = dstI + 4;
			break;
		default:
			dst[dstI] = src[srcI];
		}
	dst[dstI] = 0;
	return dst;
}

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

#ifndef CURLTYPE_H
#define CURLTYPE_H


#include <string>
#include <functional>


#include "curl.h"
#include "cmessage.h"


using std::string;
using std::binary_function;


/**
  * class CURLType
  * define interface for managing URL types; the derived classes will implement this
  * interface (getTypeName(), getURL() methods); the CURLType() constructor will
  * register this object to CURLRegister
  */

class CURLType
{
public:
	virtual ~CURLType() {}

	virtual string getTypeName() const = 0;

	virtual CURL* getURL(const CMsgURLRequest& p_rcoMsg) const = 0;

	void registerURLType();
};


// CURLType_less: function object for CURLType

struct CURLType_less : public binary_function<CURLType, CURLType, bool>
{
	bool operator ()(const CURLType* first, const CURLType* second) const
		{ return case_comp(first->getTypeName(), second->getTypeName()) < 0; }
};


#endif // CURLTYPE_H
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

Implementation of data types: Integer, String, Switch, Date, Time, DateTime, Enum

******************************************************************************/

#include <stdio.h>
#include <stdlib.h>
#include <values.h>
#include <map>

#include "data_types.h"
#include "auto_ptr.h"


// Integer implementation

// string conversion operator
Integer::operator string() const
{
	char pchBuf[20];
	
	sprintf(pchBuf, "%ld", m_nValue);
	return string(pchBuf);
}

// string2int: converts string to int; throws InvalidValue if unable to convert
long int Integer::string2int(const string& p_rcoVal) throw(InvalidValue)
{
	const char* pchStart = p_rcoVal.c_str();
	char* pchEnd;
	long int nRes = strtol(pchStart, &pchEnd, 10);
	if (nRes == LONG_MIN || nRes == LONG_MAX || pchEnd == pchStart || *pchEnd != '\0')
		throw InvalidValue();
	return nRes;
}


// Switch implementation

string Switch::s_coValName[2] = { "ON", "OFF" };

Switch::SwitchVal Switch::string2SwitchVal(const string& p_rcoVal) throw(InvalidValue)
{
	for (int i = 0; i < 2; i++)
		if (case_comp(p_rcoVal, s_coValName[i]) == 0)
			return (SwitchVal)i;
	throw InvalidValue();
}


// Date implementation

// instantiation from string; date must be of format: "yyyy" + p_coSep + "mm" + p_coSep + "dd"
Date::Date(const string& p_rcoDate, string p_coSep) throw(InvalidValue)
	: m_coSep(p_coSep)
{
	string::size_type nMon = p_rcoDate.find(p_coSep);
	string::size_type nMDay = p_rcoDate.find(p_coSep, nMon + 1);
	if (nMon == 0 || nMon == nMDay)
		throw InvalidValue();
	m_nYear = Integer::string2int(p_rcoDate.substr(0, nMon));
	m_nMon = Integer::string2int(p_rcoDate.substr(nMon + 1, nMDay - nMon - 1));
	m_nMDay = Integer::string2int(p_rcoDate.substr(nMDay + 1, p_rcoDate.length() - nMDay - 1));
	Validate();
}

// string conversion operator; returns the date in format: "yyyy" + m_coSep + "mm" + m_coSep + "dd"
Date::operator string() const
{
	return string(Integer(m_nYear))
	       + m_coSep + string(Integer(m_nMon))
	       + m_coSep + string(Integer(m_nMDay));
}

// struct tm conversion operator
Date::operator struct tm() const
{
	struct tm TM = { 0, 0, 0, m_nMDay, m_nMon - 1, m_nYear - 1900, 0, 0, 0 };
	return TM;
}

void Date::Validate() throw(InvalidValue)
{
	struct tm TM = { 0, 0, 0, m_nMDay, m_nMon - 1, m_nYear - 1900, 0, 0, 0 };
	if (mktime(&TM) == -1)
		throw InvalidValue();
	if (TM.tm_year != (m_nYear - 1900) || TM.tm_mon != (m_nMon - 1) || TM.tm_mday != m_nMDay)
		throw InvalidValue();
}


// Time implementation

// conversion from string
Time::Time(const string& p_rcoTime, string p_coSep) throw(InvalidValue)
	: m_coSep(p_coSep)
{
	string::size_type nMin = p_rcoTime.find(p_coSep);
	string::size_type nSec = p_rcoTime.find(p_coSep, nMin + 1);
	if (nMin == 0 || nMin == nSec)
		throw InvalidValue();
	m_nHour = Integer::string2int(p_rcoTime.substr(0, nMin));
	m_nMin = Integer::string2int(p_rcoTime.substr(nMin + 1, nSec - nMin - 1));
	m_nSec = Integer::string2int(p_rcoTime.substr(nSec + 1, p_rcoTime.length() - nSec - 1));
	Validate();
}

// string conversion operator
Time::operator string() const
{
	return string(Integer(m_nHour))
	       + m_coSep + string(Integer(m_nMin))
	       + m_coSep + string(Integer(m_nSec));
}

// struct tm conversion operator
Time::operator struct tm() const
{
	struct tm TM = { m_nSec, m_nMin, m_nHour, 1, 0, 0, 0, 0, 0 };
	return TM;
}

void Time::Validate() throw(InvalidValue)
{
	struct tm TM = { m_nSec, m_nMin, m_nHour, 1, 0, 2, 0, 0, 0 };
	if (mktime(&TM) == -1)
		throw InvalidValue();
	if (TM.tm_hour != m_nHour || TM.tm_min != m_nMin || TM.tm_sec != m_nSec)
		throw InvalidValue();
}


// DateTime implementation

// conversion from string
DateTime::DateTime(const string& p_rcoVal, string p_coDateSep = "-", string p_coTimeSep = ":")
	throw(InvalidValue)
	: Date(p_rcoVal.substr(0, p_rcoVal.find(" "))),
	  Time(p_rcoVal.substr(p_rcoVal.find(" ") + 1, p_rcoVal.length() - 1))
{
}

// struct tm conversion operator
DateTime::operator struct tm() const
{
	struct tm TM = { sec(), min(), hour(), mday(), mon() - 1, year() + 1900, 0, 0, 0 };
	return TM;
}


// Enum implementation

// Enum::Item constructor: instantiation from string
Enum::Item::Item(const string& p_rcoVal) throw(InvalidValue)
{
	string::size_type nSep = p_rcoVal.find(":");
	if (m_pcoEnum->name() != p_rcoVal.substr(0, nSep))
		throw InvalidValue();
	m_coItem = p_rcoVal.substr(nSep + 1, p_rcoVal.length() - nSep - 1);
}

// CValuesMap: map used to store enum values
class CValuesMap : private map<string, long int>
{
public:
	// default constructor
	CValuesMap() : m_bValuesValid(true), m_coValues("") {}

	// insert: insert value in map
	void insert(const string& val, long int id);

	// erase: erase value from map
	void erase(const string& val);

	// id: return id associated to value
	long int id(const string& val) const throw(InvalidValue);

	// has: return true if value is in map
	bool has(const string& val) const;

	// values: return string containing values in the map
	const string& values() const;

private:
	mutable bool m_bValuesValid;
	mutable string m_coValues;
};

// insert: insert value in map
inline void CValuesMap::insert(const string& val, long int id)
{
	m_bValuesValid = false;
	this->operator [](val) = id;
}

// erase: erase value from map
inline void CValuesMap::erase(const string& val)
{
	iterator coIt = find(val);
	if (coIt == end())
		return;
	m_bValuesValid = false;
	map<string, long int>::erase(coIt);
}

// id: return id associated to value
inline long int CValuesMap::id(const string& val) const throw(InvalidValue)
{
	const_iterator coIt = find(val);
	if (coIt == end())
		throw InvalidValue();
	return (*coIt).second;
}

// has: return true if value is in map
inline bool CValuesMap::has(const string& val) const
{
	return find(val) != end();
}

// values: return string containing values in the map
const string& CValuesMap::values() const
{
	if (m_bValuesValid)
		return m_coValues;
	m_coValues = "";
	bool first = true;
	for (const_iterator coIt = begin(); coIt != end(); ++coIt)
	{
		if (first)
			first = false;
		else
			m_coValues += ", ";
		m_coValues += (*coIt).first;
	}
	m_bValuesValid = true;
	return m_coValues;
}


// CEnumMap: map of enum types; all enum types are registered here
class CEnumMap : private map<string, CValuesMap*>
{
public:
	// default constructor
	CEnumMap() {}

	// destructor
	~CEnumMap();

	// insert: insert enum type
	bool insert(const string&, CValuesMap*);

	// insert: insert enum type
	bool insert(const string& p_rcoEnum, const CValuesMap& p_rcoValMap)
	{ return insert(p_rcoEnum, new CValuesMap(p_rcoValMap)); }

	// erase: erase enum type
	void erase(const string& p_rcoEnum);

	// has: returns true if enum type is registered
	bool has(const string& p_rcoEnum) const;

	// valMap: returns values map of some enum type
	const CValuesMap* valMap(const string& p_rcoEnum) const throw(InvalidValue);

	// values: return string containig values of some enun type
	const string& values(const string&) const throw(InvalidValue);

private:
	// private copy-constructor, assign and compare operators: don't allow copying and
	// comparison
	CEnumMap(const CEnumMap&);

	const CEnumMap& operator =(const CEnumMap&);

	bool operator ==(const CEnumMap&);

	bool operator !=(const CEnumMap&);
};

// CEnumMap destructor
CEnumMap::~CEnumMap()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
		map<string, CValuesMap*>::erase(coIt);
	}
}

// insert: insert enum type
bool CEnumMap::insert(const string& p_rcoEnum, CValuesMap* p_pcoValMap)
{
	if (p_pcoValMap == NULL)
		return false;
	iterator coIt = find(p_rcoEnum);
	if (coIt != end())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
	}
	this->operator[](p_rcoEnum) = p_pcoValMap;
	return true;
}

// erase: erase enum type
void CEnumMap::erase(const string& p_rcoEnum)
{
	iterator coIt = find(p_rcoEnum);
	if (coIt == end())
		return;
	delete (*coIt).second;
	(*coIt).second = NULL;
	map<string, CValuesMap*>::erase(coIt);
}

// has: returns true if enum type is registered
inline bool CEnumMap::has(const string& p_rcoEnum) const
{
	return find(p_rcoEnum) != end();
}

// valMap: returns values map of some enum type
inline const CValuesMap* CEnumMap::valMap(const string& p_rcoEnum) const throw(InvalidValue)
{
	const_iterator coIt = find(p_rcoEnum);
	if (coIt == end())
		throw InvalidValue();
	return (*coIt).second;
}

// values: return string containig values of some enun type
inline const string& CEnumMap::values(const string& p_rcoEnum) const throw(InvalidValue)
{
	const_iterator coIt = find(p_rcoEnum);
	if (coIt == end())
		throw InvalidValue();
	return (*coIt).second->values();
}

// initialise enums map
CEnumMap* Enum::s_pcoEnums = new CEnumMap;

// Enum constructor: initialise it from a string (name) and a list of pair values; the list
// must be ordered by the data type: long int; if the data value is -1 it is automatically
// generated; throws InvalidValue if list contains two or more pairs having the same key or
// is not ordered
Enum::Enum(const string& p_rcoName, const list<pair<string, long int> >& p_rcoValues)
	throw(InvalidValue, bad_alloc) : m_coName(p_rcoName)
{
	registerEnum(p_rcoName, p_rcoValues);
}

// returns an item identified by name; throws InvalidValue if invalid item name
Enum::Item Enum::item(const string& p_rcoVal) const throw(InvalidValue)
{
	const CValuesMap* pcoValMap = s_pcoEnums->valMap(m_coName);
	if (!pcoValMap->has(p_rcoVal))
		throw InvalidValue();
	return Item(p_rcoVal, *this);
}

// returns the value of an item identified by name; throws InvalidValue if invalid item name
long int Enum::itemValue(const string& p_rcoVal) const throw(InvalidValue)
{
	return s_pcoEnums->valMap(m_coName)->id(p_rcoVal);
}

// values: return enum values
const string& Enum::values() const throw(InvalidValue)
{
	return s_pcoEnums->valMap(m_coName)->values();
}

// values: return enum values of enum type
const string& Enum::values(const string& p_rcoEnum) throw(InvalidValue)
{
	return s_pcoEnums->valMap(p_rcoEnum)->values();
}

// enumObj: return enum object of type p_rcoEnum
Enum* Enum::enumObj(const string& p_rcoEnum) throw(InvalidValue)
{
	if (!s_pcoEnums->has(p_rcoEnum))
		throw InvalidValue();
	return new Enum(p_rcoEnum);
}

// isValid: return true if enum type exists
bool Enum::isValid(const string& p_rcoEnum)
{
	return s_pcoEnums->has(p_rcoEnum);
}

// registerEnum: add enum to enum types
void Enum::registerEnum(const string& p_rcoName,
	                    const list<pair<string, long int> >& p_rcoValues)
	throw(InvalidValue, bad_alloc)
{
	SafeAutoPtr<CValuesMap> pcoValues(new CValuesMap);
	list<pair<string, long int> >::const_iterator coIt;
	long int i = 1;
	long int nMax = 0;
	for (coIt = p_rcoValues.begin(); coIt != p_rcoValues.end(); ++coIt)
	{
		long int nVal = (*coIt).second;
		if (nVal < 0)
			nVal = i;
		if (nMax >= nVal)
			throw InvalidValue();
		nMax = nVal;
		string coName = (*coIt).first;
		if (pcoValues->has(coName))
			throw InvalidValue();
		pcoValues->insert(coName, nVal);
		i = nVal + 1;
	}
	s_pcoEnums->insert(p_rcoName, pcoValues.release());
}

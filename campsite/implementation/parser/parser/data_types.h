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

Declares data types: Integer, String, Switch, Date, Time, DateTime, Enum

******************************************************************************/

#ifndef _CMS_DATA_TYPES
#define _CMS_DATA_TYPES

#include <time.h>

#include <string>
#include <stdexcept>
#include <list>

#include "globals.h"


// Integer data type; wrapper around int
class Integer
{
public:
	// default constructor
	Integer(long int p_nVal = 0) : m_nValue(p_nVal) {}

	// conversion from string
	Integer(const string& p_rcoVal) throw(InvalidValue) { m_nValue = string2int(p_rcoVal); }

	// string conversion operator
	operator string() const;

	// int conversion operator
	operator long int() const { return m_nValue; }

	// comparison operators
	bool operator ==(const Integer& p_rcoOther) const
	{ return m_nValue == p_rcoOther.m_nValue; }

	bool operator !=(const Integer& p_rcoOther) const
	{ return m_nValue != p_rcoOther.m_nValue; }

	bool operator >(const Integer& p_rcoOther) const
	{ return m_nValue > p_rcoOther.m_nValue; }

	bool operator >=(const Integer& p_rcoOther) const
	{ return m_nValue >= p_rcoOther.m_nValue; }

	bool operator <(const Integer& p_rcoOther) const
	{ return m_nValue < p_rcoOther.m_nValue; }

	bool operator <=(const Integer& p_rcoOther) const
	{ return m_nValue <= p_rcoOther.m_nValue; }

	// string2int: converts string to int; throws InvalidValue if unable to convert
	static long int string2int(const string&) throw(InvalidValue);

private:
	long int m_nValue;
};


// String data type; wrapper around string
class String
{
public:
	// default constructor
	String(const string& p_rcoVal = "") : m_coValue(p_rcoVal) {}

	// int conversion operator
	operator string() const { return m_coValue; }

	bool operator ==(const String& p_rcoOther) const
	{ return m_coValue == p_rcoOther.m_coValue; }

	bool operator !=(const String& p_rcoOther) const
	{ return m_coValue != p_rcoOther.m_coValue; }

	bool operator >(const String& p_rcoOther) const
	{ return m_coValue > p_rcoOther.m_coValue; }

	bool operator >=(const String& p_rcoOther) const
	{ return m_coValue >= p_rcoOther.m_coValue; }

	bool operator <(const String& p_rcoOther) const
	{ return m_coValue < p_rcoOther.m_coValue; }

	bool operator <=(const String& p_rcoOther) const
	{ return m_coValue <= p_rcoOther.m_coValue; }

	bool case_equal(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) == 0; }

	bool case_not_equal(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) != 0; }

	bool case_greater(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) > 0; }

	bool case_greater_equal(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) >= 0; }

	bool case_less(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) < 0; }

	bool case_less_equal(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) <= 0; }

private:
	string m_coValue;
};


// Switch data type
class Switch
{
public:
	typedef enum { OFF = 0, ON = 1 } SwitchVal;

	// default constructor
	Switch(SwitchVal p_nVal = OFF) : m_nValue(p_nVal) {}

	// conversion from string
	Switch(const string& p_rcoVal) throw(InvalidValue) { m_nValue = string2SwitchVal(p_rcoVal); }

	// string conversion operator
	operator string() const { return valName(); }

	// SwitchVal conversion operator
	operator SwitchVal() const { return m_nValue; }

	bool operator ==(const Switch& p_rcoOther) const
	{ return m_nValue == p_rcoOther.m_nValue; }

	bool operator !=(const Switch& p_rcoOther) const
	{ return m_nValue != p_rcoOther.m_nValue; }

	static const string& valName(SwitchVal p_rnVal) { return s_coValName[(int)p_rnVal]; }

	const string& valName() const { return valName(m_nValue); }

	static SwitchVal string2SwitchVal(const string&) throw(InvalidValue);

private:
	SwitchVal m_nValue;

	static string s_coValName[2];
};


// Date data type
class Date
{
public:
	// instantiation from string; date must be of format: "yyyy" + p_coSep + "mm" + p_coSep + "dd"
	Date(const string&, string p_coSep = "-") throw(InvalidValue);

	// instantiation from struct tm
	Date(const struct tm& p_tm, string p_coSep = "-") throw(InvalidValue)
		: m_nYear(p_tm.tm_year+1900), m_nMon(p_tm.tm_mon + 1), m_nMDay(p_tm.tm_mday),
		  m_coSep(p_coSep) { Validate(); }

	Date(int p_nYear, int p_nMon, int p_nMDay, string p_coSep = "-") throw(InvalidValue)
		: m_nYear(p_nYear), m_nMon(p_nMon), m_nMDay(p_nMDay), m_coSep(p_coSep) { Validate(); }

	// virtual destructor
	virtual ~Date() {}

	// access members
	int year() const { return m_nYear; }
	int mon() const { return m_nMon; }
	int mday() const { return m_nMDay; }

	// string conversion operator; returns the date in format: "yyyy" + m_coSep + "mm"
	// + m_coSep + "dd"
	virtual operator string() const;

	// struct tm conversion operator
	virtual operator struct tm() const;

	bool operator ==(const Date& p_rcoOther) const;

	bool operator !=(const Date& p_rcoOther) const { return ! (*this == p_rcoOther); }

	bool operator >(const Date& p_rcoOther) const;

	bool operator >=(const Date& p_rcoOther) const
	{ return *this > p_rcoOther || *this == p_rcoOther; }

	bool operator <(const Date& p_rcoOther) const;

	bool operator <=(const Date& p_rcoOther) const
	{ return *this < p_rcoOther || *this == p_rcoOther; }

private:
	void Validate() throw(InvalidValue);

	int m_nYear;
	int m_nMon;
	int m_nMDay;

	mutable string m_coSep;
};

// some Date inline methods
inline bool Date::operator ==(const Date& p_rcoOther) const
{
	return m_nYear == p_rcoOther.m_nYear && m_nMon == p_rcoOther.m_nMon
	       && m_nMDay == p_rcoOther.m_nMDay;
}

inline bool Date::operator >(const Date& p_rcoOther) const
{
	return m_nYear > p_rcoOther.m_nYear
	       || (m_nYear == p_rcoOther.m_nYear && m_nMon > p_rcoOther.m_nMon)
	       || (m_nYear == p_rcoOther.m_nYear && m_nMon == p_rcoOther.m_nMon
	           && m_nMDay > p_rcoOther.m_nMDay);
}

inline bool Date::operator <(const Date& p_rcoOther) const
{
	return m_nYear < p_rcoOther.m_nYear
	       || (m_nYear == p_rcoOther.m_nYear && m_nMon < p_rcoOther.m_nMon)
	       || (m_nYear == p_rcoOther.m_nYear && m_nMon == p_rcoOther.m_nMon
	           && m_nMDay < p_rcoOther.m_nMDay);
}


// Time data type
class Time
{
public:
	// conversion from string; time must be of format: "hh" + p_coSep + "mm" + p_coSep + "ss"
	Time(const string&, string p_coSep = ":") throw(InvalidValue);

	// instantiation from struct tm
	Time(const struct tm& p_tm, string p_coSep = ":") throw(InvalidValue)
		: m_nHour(p_tm.tm_hour), m_nMin(p_tm.tm_min), m_nSec(p_tm.tm_sec), m_coSep(p_coSep)
	{ Validate(); }

	Time(int p_nHour, int p_nMin, int p_nSec, string p_coSep = ":") throw(InvalidValue)
		: m_nHour(p_nHour), m_nMin(p_nMin), m_nSec(p_nSec), m_coSep(p_coSep) { Validate(); }

	// virtual destructor
	virtual ~Time() {}

	// access members
	int hour() const { return m_nHour; }
	int min() const { return m_nMin; }
	int sec() const { return m_nSec; }

	// string conversion operator; returns the time in format: "hh" + m_coSep + "mm" + m_coSep
	// + "ss"
	virtual operator string() const;

	// struct tm conversion operator
	virtual operator struct tm() const;

	bool operator ==(const Time& p_rcoOther) const;

	bool operator !=(const Time& p_rcoOther) const { return ! (*this == p_rcoOther); }

	bool operator >(const Time& p_rcoOther) const;

	bool operator >=(const Time& p_rcoOther) const
	{ return *this > p_rcoOther || *this == p_rcoOther; }

	bool operator <(const Time& p_rcoOther) const;

	bool operator <=(const Time& p_rcoOther) const
	{ return *this < p_rcoOther || *this == p_rcoOther; }

private:
	void Validate() throw(InvalidValue);

	int m_nHour;
	int m_nMin;
	int m_nSec;

	mutable string m_coSep;
};

// some Time inline methods
inline bool Time::operator ==(const Time& p_rcoOther) const
{
	return m_nHour == p_rcoOther.m_nHour
	       && m_nMin == p_rcoOther.m_nMin
	       && m_nSec == p_rcoOther.m_nSec;
}

inline bool Time::operator >(const Time& p_rcoOther) const
{
	return m_nHour > p_rcoOther.m_nHour
	       || (m_nHour == p_rcoOther.m_nHour && m_nMin > p_rcoOther.m_nMin)
	       || (m_nHour == p_rcoOther.m_nHour && m_nMin == p_rcoOther.m_nMin
	           && m_nSec > p_rcoOther.m_nSec);
}

inline bool Time::operator <(const Time& p_rcoOther) const
{
	return m_nHour < p_rcoOther.m_nHour
	       || (m_nHour == p_rcoOther.m_nHour && m_nMin < p_rcoOther.m_nMin)
	       || (m_nHour == p_rcoOther.m_nHour && m_nMin == p_rcoOther.m_nMin
	           && m_nSec < p_rcoOther.m_nSec);
}


// DateTime data type
class DateTime : public Date, public Time
{
public:
	// conversion from string; date must be of format:
	// "yyyy" + p_coSep + "mm" + p_coSep + "dd" + " " + "hh" + m_coSep + "mm" + m_coSep + "ss"
	DateTime(const string&, string p_coDateSep = "-", string p_coTimeSep = ":")
		throw(InvalidValue);

	// instantiation from struct tm
	DateTime(const struct tm& p_tm, string p_coDateSep = "-", string p_coTimeSep = ":")
		throw(InvalidValue)
		: Date(p_tm, p_coDateSep), Time(p_tm, p_coTimeSep) {}

	DateTime(int p_nYear, int p_nMon, int p_nMDay, int p_nHour, int p_nMin, int p_nSec,
	         string p_coDateSep = "-", string p_coTimeSep = ":") throw(InvalidValue)
		: Date(p_nYear, p_nMon, p_nMDay, p_coDateSep), Time(p_nHour, p_nMin, p_nSec, p_coTimeSep) {}

	// virtual destructor
	virtual ~DateTime() {}

	// string conversion operator; returns the time in format:
	// "yyyy" + p_coSep + "mm" + p_coSep + "dd" + " " + "hh" + m_coSep + "mm" + m_coSep + "ss"
	virtual operator string() const
	{ return Date::operator string() + " " + Time::operator string(); }

	// struct tm conversion operator
	virtual operator struct tm() const;

	bool operator ==(const DateTime& p_rcoOther) const;

	bool operator !=(const DateTime& p_rcoOther) const { return ! (*this == p_rcoOther); }

	bool operator >(const DateTime& p_rcoOther) const;

	bool operator >=(const DateTime& p_rcoOther) const
	{ return *this > p_rcoOther || *this == p_rcoOther; }

	bool operator <(const DateTime& p_rcoOther) const;

	bool operator <=(const DateTime& p_rcoOther) const
	{ return *this < p_rcoOther || *this == p_rcoOther; }
};

// some DateTime inline methods
inline bool DateTime::operator ==(const DateTime& p_rcoOther) const
{
	return Date::operator ==(p_rcoOther)
	       && Time::operator ==(p_rcoOther);
}

inline bool DateTime::operator >(const DateTime& p_rcoOther) const
{
	return Date::operator >(p_rcoOther)
	       || (Date::operator ==(p_rcoOther) && Time::operator >(p_rcoOther));
}

inline bool DateTime::operator <(const DateTime& p_rcoOther) const
{
	return Date::operator <(p_rcoOther)
	       || (Date::operator ==(p_rcoOther) && Time::operator <(p_rcoOther));
}


class CEnumMap;

// Enum data type
class Enum
{
public:
	// Enum item
	class Item
	{
		friend class Enum;

	public:
		// instantiation from string
		Item(const string& p_rcoVal) throw(InvalidValue);

		// conversion to string
		operator string() const { return m_pcoEnum->name() + ":" + name(); }

		// item name
		const string& name() const { return m_coItem; }

		// item value
		long int value() const { return m_pcoEnum->itemValue(m_coItem); }

		// comparison operator
		bool operator ==(const Item& p_rcoVal) const
		{
			return m_coItem == p_rcoVal.m_coItem
			       && m_pcoEnum->name() == p_rcoVal.m_pcoEnum->name();
		}

		// comparison operator
		bool operator !=(const Item& p_rcoVal) const { return ! (*this == p_rcoVal); }

	private:
		// constructor
		Item(const string& p_coVal, const Enum& p_coEnum)
			: m_coItem(p_coVal), m_pcoEnum(&p_coEnum) {}

	private:
		string m_coItem;
		const Enum* m_pcoEnum;
	};

public:
	// Enum constructor: initialise it from a string (name) and a list of pair values; the list
	// must be ordered by the data type: long int; if the data value is -1 it is automatically
	// generated; throws InvalidValue if list contains two or more pairs having the same key or
	// is not ordered
	Enum(const string&, const list<pair<string, long int> >&) throw(InvalidValue, bad_alloc);

	// returns an item identified by name; throws InvalidValue if invalid item name
	Item item(const string& p_rcoVal) const throw(InvalidValue);

	// returns the value of an item identified by name; throws InvalidValue if invalid item name
	long int itemValue(const string& p_rcoVal) const throw(InvalidValue);

	// name: return enum type name
	const string& name() const { return m_coName; }

	// values: return enum values
	const string& values() const throw(InvalidValue);

	// values: return enum values of enum type
	static const string& values(const string& p_rcoEnum) throw(InvalidValue);

	// enumObj: return enum object of type p_rcoEnum
	static Enum* enumObj(const string& p_rcoEnum) throw(InvalidValue);

	// isValid: return true if enum type exists
	static bool isValid(const string& p_rcoEnum);

	// registerEnum: add enum to enum types
	static void registerEnum(const string&, const list<pair<string, long int> >&)
		throw(InvalidValue, bad_alloc);

private:
	Enum(const string& p_rcoEnum) : m_coName(p_rcoEnum) {}

private:
	string m_coName;
	static CEnumMap* s_pcoEnums;
};

#endif

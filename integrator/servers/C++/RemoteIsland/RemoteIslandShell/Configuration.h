/**
 **	File ......... Configuration.h
 **	Published ....  2004-07-13
 **	Author ....... grymse@alhem.net
**/
/*
Copyright (C) 2004  Anders Hedstrom

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
#ifndef _CONFIGURATION_H
#define _CONFIGURATION_H

#include "BaseXMLFile.h"


class Configuration : public BaseXMLFile
{
public:
	Configuration(const std::string& filename);
	~Configuration();

	xmlNodePtr Find(xmlNodePtr,const std::string& );

};




#endif // _CONFIGURATION_H

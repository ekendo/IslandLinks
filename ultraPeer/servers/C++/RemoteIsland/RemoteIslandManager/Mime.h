/**
 **	File ......... Mime.h
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
#ifndef _MIME_H
#define _MIME_H

#include <string>
#include <map>

typedef std::map<std::string,std::string> mime_m;


class Mime
{
public:
	Mime(const std::string& );
	~Mime();

	std::string GetMimeFromExtension(const std::string& );

private:
	mime_m m_mime;
};




#endif // _MIME_H

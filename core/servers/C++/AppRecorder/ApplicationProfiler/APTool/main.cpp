

#include "APTool.h"


// Another helper function to simplify configuration parsing
template<class T>
std::string& totalString(const vector<T>& v)
{
	std::string total = "";
	
	for(int i=0; i<v.size(); i++)
	{
		total+= v[i];
	}
	
	return total;
	
}

static std::map<std::string,std::string> generic_op_bucket;

// Helper file to handle the regex
static bool regex_callback(const boost::match_results<std::string::const_iterator>& what)
{
	std::string po_value;
	
	printf("called back\n");
	
	if(what[0].matched)
	{
		po_value = what[1].str().c_str();
		printf("group match with value:%s ",po_value.c_str());
		printf("With an overall match of %s\n", what[0].str().c_str());
		printf("And a pair match of [%s,%s]\n", what[1].str().c_str(), what[2].str().c_str());
	}
	else
	{
		printf("apparently there was no match at all!\n");
	}
	
	generic_op_bucket.insert ( pair<std::string,std::string>(what[1].str(), what[2].str()) );
	
	return true;
}

void usage()
{
	printf("ApplicationProfilerTool, version %s\n"
		   "Use: %s [ -f | -d | -i | -l | -n | -a | -c | -e | -s | -k ] \n"
		   ,"00.00.01", "APTool");
	
	printf("\t-f run in foreground\n");
	printf("\t-d run in debug mode\n");
	printf("\t-i regex instructions\n");
	printf("\t-l regex libraries\n");
	printf("\t-n regex native functions\n");
	printf("\t-a regex api functions\n");
	printf("\t-c load custom config\n");
	printf("\t-e load custom emulator\n");
	printf("\t-s load custom simulator\n");
	printf("\t-k load custom kernel profiler\n");
	
	exit(1);
}

bool APTool::ConnectToDefaultDB(DEFAULT_DB d)
{
	bool connected = false;
	
	mysqlpp::Connection con(mysqlpp::use_exceptions);
	
	try
	{
		if(this->m_debug&&(!this->m_daemonValue))
		{
			printf("Name:%s\n",d.name.c_str());
			printf("Host:%s\n",d.host.c_str());
			printf("User:%s\n",d.user.c_str());
			printf("Pass:%s\n",d.pass.c_str());
		}
		
		if(this->m_daemonValue&&this->m_debug)
		{
			syslog(LOG_INFO, "Connecting to Default DataBase with:%s,%s,%s,%s",d.name.c_str(), d.host.c_str(), d.user.c_str(), d.pass.c_str());
		}
		
		if(!con.connect(d.name.c_str(), d.host.c_str(), d.user.c_str(), d.pass.c_str()))
		{
			if(this->m_debug&&this->m_daemonValue)
			{
				printf("not connected\n");
				
			}
			
			if(this->m_daemonValue)
			{
				syslog(LOG_ERR,	"not connected\n");
			}
			
		}
		else
		{
			if(this->m_debug&&(!this->m_daemonValue))
			{
				printf(" connected! to default DB:%s\n",d.name.c_str());
			}
			
			if(this->m_daemonValue&&this->m_debug)
			{
				syslog(LOG_INFO,"connected to default DB:%s\n", d.name.c_str());
			}
			
			connected = true;
			this->m_currentDB_Conn = con;
			this->m_currentDefaultDB = d;
		}
	}
	catch(exception& e)
	{
		if(this->m_debug&&(!this->m_daemonValue))
		{
			cout<<e.what()<<"\n";
		}
		
		if(this->m_daemonValue)
		{
			syslog(LOG_ERR,	"%s\n",e.what());
		}
		
	}
	
	return connected;
}

bool APTool::ConnectToDefaultDB()
{
	bool connected = false;
	
	mysqlpp::Connection con(mysqlpp::use_exceptions);
	
	try
	{
		if(this->m_debug&&(!this->m_daemonValue))
		{
			printf("Name:%s\n",this->m_currentDefaultDB.name.c_str());
			printf("Host:%s\n",this->m_currentDefaultDB.host.c_str());
			printf("User:%s\n",this->m_currentDefaultDB.user.c_str());
			printf("Pass:%s\n",this->m_currentDefaultDB.pass.c_str());
		}
		
		if(this->m_daemonValue&&this->m_debug)
		{
			syslog(LOG_INFO, "Connecting to Default DataBase with:%s,%s,%s,%s",this->m_currentDefaultDB.name.c_str(), this->m_currentDefaultDB.host.c_str(), this->m_currentDefaultDB.user.c_str(), this->m_currentDefaultDB.pass.c_str());
		}
		
		if(!con.connect(this->m_currentDefaultDB.name.c_str(), this->m_currentDefaultDB.host.c_str(), this->m_currentDefaultDB.user.c_str(), this->m_currentDefaultDB.pass.c_str()))
		{
			if(this->m_debug&&this->m_daemonValue)
			{
				printf("not connected\n");
				
			}
			
			if(this->m_daemonValue)
			{
				syslog(LOG_ERR,	"not connected\n");
			}
			
		}
		else
		{
			if(this->m_debug&&(!this->m_daemonValue))
			{
				printf(" connected! to default DB:%s\n",this->m_currentDefaultDB.name.c_str());
			}
			
			if(this->m_daemonValue&&this->m_debug)
			{
				syslog(LOG_INFO,"connected to default DB:%s\n", this->m_currentDefaultDB.name.c_str());
			}
			
			connected = true;
			this->m_currentDB_Conn = con;
		}
	}
	catch(exception& e)
	{
		if(this->m_debug&&(!this->m_daemonValue))
		{
			cout<<e.what()<<"\n";
		}
		
		if(this->m_daemonValue)
		{
			syslog(LOG_ERR,	"%s\n",e.what());
		}
		
	}
	
	return connected;
}

bool APTool::LoadAppROM(std::string file_loc, std::string start, std::string end, int off)
{
	bool appRomLoaded = false;
	APP_ROM_INFO * ari;
	//char * buffer;
	//char int_buffer[33];
	//ifstream is;
	//int length;
	
	try
	{
		ari = new APP_ROM_INFO();
		ari->filePath = file_loc.c_str();
		//ari->offset = _itoa(off,int_buffer,10);
		ari->fT = this->m_exe_format;
		ari->sectionT = "__text";
		
		this->m_Roms.push_back(ari);
		this->m_apc_settings.a_r_info.push_back(ari);
		
		//delete[33] int_buffer;
		
		appRomLoaded = true;
	}
	catch(exception& e)
	{
		if(this->m_debug)
		{
			printf("[ERROR] problems loading app Rom:%s\n", e.what());
		}
	}
	
	return appRomLoaded;
}

void APTool::SetDebug(bool d)
{
	this->m_debug = d;
	
}

bool APTool::CreateStaticDataProfile()
{
	bool profileCreated = false;
	ifstream romFile;
	std::string romContent = "";
	//std::vector<boost::regex> operation_regexes;
	// iterators
	std::vector<boost::regex>::const_iterator or_it;
	std::string::const_iterator rcb_it, rce_it;
	boost::match_results<std::string::const_iterator> what;
	std::map<std::string, std::string>::const_iterator op_cat_it;
	
	try
	{
		/* Get the disasm file from somewhere */
		APP_ROM_INFO * ri  = this->m_Roms.back();
		
		if(this->m_debug)
		{
			printf("The Rom/Asm file that we will be using today is here:%s\n",ri->filePath.c_str());
		}
		
		romFile.open(ri->filePath.c_str(), ifstream::in);
		while(romFile.good())
			romContent += romFile.get();
		romFile.close();
		
		if(this->m_debug)// maybe for verbose mode
		{
			//printf("This is what was in the loaded Rom File:%s\n",romContent.c_str());
		}
		
		rcb_it = romContent.begin();
		rce_it = romContent.end();
		
		//boost::regex dcb("[0-9A-Fa-f]+[\s]+DCB.*");
		//[\sa-zA-Z]+\s([a-zA-Z:\"]+)\sDCB]\s([0-9A-Fa-f]+[\sa-zA-Z]+)
		boost::regex dcb("([0-9A-Fa-f]+)([\sa-zA-Z\s]+)");//DCB[\s\"]([a-zA-Z:]+)");
		//operation_regexes.push_back(al);
		boost::sregex_iterator dcb1(rcb_it, rce_it, dcb);
		boost::sregex_iterator dcb2;
		std::for_each(dcb1,dcb2, &regex_callback);
		
		// dump static container content 
		for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
		{
			this->m_StaticData.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
			//this->m_CondInstructions.insert(pair<std::string,std::string>("",""));
			
		}
	}
	catch(exception& e)
	{
		if(this->m_debug)
		{
			printf("[ERROR] problems creating static data profile:%s\n",e.what());
		}
	}
		
}

bool APTool::CreateInstructionProfile()
{
	bool profileCreated = false;
	ifstream romFile;
	std::string romContent = "";
	//std::vector<boost::regex> operation_regexes;
	// iterators
	std::vector<boost::regex>::const_iterator or_it;
	std::string::const_iterator rcb_it, rce_it;
	boost::match_results<std::string::const_iterator> what;
	std::map<std::string, std::string>::const_iterator op_cat_it;
	
	try
	{
		/* Get the disasm file from somewhere */
		APP_ROM_INFO * ri  = this->m_Roms.back();
		
		if(this->m_debug)
		{
			printf("The Rom/Asm file that we will be using today is here:%s\n",ri->filePath.c_str());
		}
		
		romFile.open(ri->filePath.c_str(), ifstream::in);
		while(romFile.good())
			romContent += romFile.get();
		romFile.close();
		
		if(this->m_debug)// maybe for verbose mode
		{
			//printf("This is what was in the loaded Rom File:%s\n",romContent.c_str());
		}
		
		rcb_it = romContent.begin();
		rce_it = romContent.end();
		
		/* create regex's for each ARMv6 operation category*/
		if(this->m_arch == ARMv6)
		{
			/* Conditional Codes */
			//boost::regex eq("([0-9A-Fa-f]+[\s]+EQ.*$/xi)");
			boost::regex eq("([0-9A-Fa-f]+)[\s]+(EQ.*)$");
			//operation_regexes.push_back(eq);
			boost::sregex_iterator eq1(rcb_it, rce_it, eq);
			boost::sregex_iterator eq2;
			std::for_each(eq1,eq2, &regex_callback);
			
			
			boost::regex ne("([0-9A-Fa-f]+)[\s]+(NE.*$)");
			//operation_regexes.push_back(ne);
			boost::sregex_iterator ne1(rcb_it, rce_it, ne);
			boost::sregex_iterator ne2;
			std::for_each(ne1,ne2, &regex_callback);
		
			
			boost::regex cs("([0-9A-Fa-f]+[\s]+CS.*$)");
			//operation_regexes.push_back(cs);
			boost::sregex_iterator cs1(rcb_it, rce_it, cs);
			boost::sregex_iterator cs2;
			std::for_each(cs1,cs2, &regex_callback);
			
			boost::regex hs("([0-9A-Fa-f]+[\s]+HS.*$)");
			//operation_regexes.push_back(hs);
			boost::sregex_iterator hs1(rcb_it, rce_it, hs);
			boost::sregex_iterator hs2;
			std::for_each(hs1,hs2, &regex_callback);
			
			boost::regex cc("([0-9A-Fa-f]+[\s]+CC.*$)");
			//operation_regexes.push_back(cc);
			boost::sregex_iterator cc1(rcb_it, rce_it, cc);
			boost::sregex_iterator cc2;
			std::for_each(cc1,cc2, &regex_callback);
			
			boost::regex lo("([0-9A-Fa-f]+[\s]+LO.*$)");
			//operation_regexes.push_back(lo);
			boost::sregex_iterator lo1(rcb_it, rce_it, lo);
			boost::sregex_iterator lo2;
			std::for_each(lo1,lo2, &regex_callback);
			
			boost::regex mi("([0-9A-Fa-f]+[\s]+MI.*$)");
			//operation_regexes.push_back(mi);
			boost::sregex_iterator mi1(rcb_it, rce_it, mi);
			boost::sregex_iterator mi2;
			std::for_each(mi1,mi2, &regex_callback);
			
			boost::regex pl("([0-9A-Fa-f]+[\s]+PL.*$)");
			//operation_regexes.push_back(pl);
			boost::sregex_iterator pl1(rcb_it, rce_it, pl);
			boost::sregex_iterator pl2;
			std::for_each(pl1,pl2, &regex_callback);
			
			boost::regex vs("([0-9A-Fa-f]+[\s]+VS.*$)");
			//operation_regexes.push_back(vs);
			boost::sregex_iterator vs1(rcb_it, rce_it, vs);
			boost::sregex_iterator vs2;
			std::for_each(vs1,vs2, &regex_callback);
			/**/
			boost::regex vc("([0-9A-Fa-f]+[\s]+VC.*$)");
			//operation_regexes.push_back(vc);
			boost::sregex_iterator vc1(rcb_it, rce_it, vc);
			boost::sregex_iterator vc2;
			std::for_each(vc1,vc2, &regex_callback);
			
			boost::regex hi("([0-9A-Fa-f]+[\s]+HI.*$)");
			//operation_regexes.push_back(ne);
			boost::sregex_iterator hi1(rcb_it, rce_it, hi);
			boost::sregex_iterator hi2;
			std::for_each(hi1,hi2, &regex_callback);
			
			boost::regex ls("([0-9A-Fa-f]+[\s]+LS.*$)");
			//operation_regexes.push_back(ls);
			boost::sregex_iterator ls1(rcb_it, rce_it, ls);
			boost::sregex_iterator ls2;
			std::for_each(ls1,ls2, &regex_callback);
			
			boost::regex ge("([0-9A-Fa-f]+[\s]+GE.*$)");
			//operation_regexes.push_back(ge);
			boost::sregex_iterator ge1(rcb_it, rce_it, ge);
			boost::sregex_iterator ge2;
			std::for_each(ge1,ge2, &regex_callback);
			/**/
			boost::regex lt("([0-9A-Fa-f]+[\s]+LT.*$)");
			//operation_regexes.push_back(lt);
			boost::sregex_iterator lt1(rcb_it, rce_it, lt);
			boost::sregex_iterator lt2;
			std::for_each(lt1,lt2, &regex_callback);
			
			boost::regex gt("([0-9A-Fa-f]+[\s]+GT.*$)");
			//operation_regexes.push_back(gt);
			boost::sregex_iterator gt1(rcb_it, rce_it, gt);
			boost::sregex_iterator gt2;
			std::for_each(gt1,gt2, &regex_callback);
			
			boost::regex le("([0-9A-Fa-f]+[\s]+LE.*$)");
			//operation_regexes.push_back(le);
			boost::sregex_iterator le1(rcb_it, rce_it, le);
			boost::sregex_iterator le2;
			std::for_each(le1,le2, &regex_callback);
			
			boost::regex al("([0-9A-Fa-f]+[\s]+AL.*$)");
			//operation_regexes.push_back(al);
			boost::sregex_iterator al1(rcb_it, rce_it, al);
			boost::sregex_iterator al2;
			std::for_each(al1,al2, &regex_callback);
			
			// dump static container content 
			for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
			{
				this->m_CondInstructions.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
				//this->m_CondInstructions.insert(pair<std::string,std::string>("",""));
				
			}
			
			// and clear
			generic_op_bucket.clear();
													  
			/* Data ops */
			boost::regex adc("([0-9A-Fa-f]+[\s]+ADC.*$)");
			boost::sregex_iterator adc1(rcb_it, rce_it, adc);
			boost::sregex_iterator adc2;
			std::for_each(adc1,adc2, &regex_callback);
			
			boost::regex add("([0-9A-Fa-f]+[\s]+ADD.*$)");
			boost::sregex_iterator add1(rcb_it, rce_it, add);
			boost::sregex_iterator add2;
			std::for_each(add1,add2, &regex_callback);
			
			boost::regex _and("([0-9A-Fa-f]+[\s]+AND.*$)");
			boost::sregex_iterator and1(rcb_it, rce_it, _and);
			boost::sregex_iterator and2;
			std::for_each(and1,and2, &regex_callback);
			
			boost::regex bic("([0-9A-Fa-f]+[\s]+BIC.*$)");
			boost::sregex_iterator bic1(rcb_it, rce_it, bic);
			boost::sregex_iterator bic2;
			std::for_each(bic1,bic2, &regex_callback);
			
			boost::regex cmn("([0-9A-Fa-f]+[\s]+CMN.*$)");
			boost::sregex_iterator cmn1(rcb_it, rce_it, cmn);
			boost::sregex_iterator cmn2;
			std::for_each(cmn1,cmn2, &regex_callback);
			
			boost::regex cmp("([0-9A-Fa-f]+[\s]+CMP.*$)");
			boost::sregex_iterator cmp1(rcb_it, rce_it, cmp);
			boost::sregex_iterator cmp2;
			std::for_each(cmp1,cmp2, &regex_callback);
			
			boost::regex eor("([0-9A-Fa-f]+[\s]+EOR.*$)");
			boost::sregex_iterator eor1(rcb_it, rce_it, eor);
			boost::sregex_iterator eor2;
			std::for_each(eor1,eor2, &regex_callback);
			
			boost::regex mov("([0-9A-Fa-f]+[\s]+MOV.*$)");
			boost::sregex_iterator mov1(rcb_it, rce_it, mov);
			boost::sregex_iterator mov2;
			std::for_each(mov1,mov2, &regex_callback);
			
			boost::regex mvn("([0-9A-Fa-f]+[\s]+MVN.*$)");
			boost::sregex_iterator mvn1(rcb_it, rce_it, mvn);
			boost::sregex_iterator mvn2;
			std::for_each(mvn1,mvn2, &regex_callback);
			
			boost::regex orr("([0-9A-Fa-f]+[\s]+ORR.*$)");
			boost::sregex_iterator orr1(rcb_it, rce_it, orr);
			boost::sregex_iterator orr2;
			std::for_each(orr1,orr2, &regex_callback);
			
			boost::regex rsb("([0-9A-Fa-f]+[\s]+RSB.*$)");
			boost::sregex_iterator rsb1(rcb_it, rce_it, rsb);
			boost::sregex_iterator rsb2;
			std::for_each(rsb1,rsb2, &regex_callback);
			
			boost::regex rsc("([0-9A-Fa-f]+[\s]+RSC.*$)");
			boost::sregex_iterator rsc1(rcb_it, rce_it, rsc);
			boost::sregex_iterator rsc2;
			std::for_each(rsc1,rsc2, &regex_callback);
			
			boost::regex sbc("([0-9A-Fa-f]+[\s]+SBC.*$)");
			boost::sregex_iterator sbc1(rcb_it, rce_it, sbc);
			boost::sregex_iterator sbc2;
			std::for_each(sbc1,sbc2, &regex_callback);
			
			boost::regex sub("([0-9A-Fa-f]+[\s]+SUB.*$)");
			boost::sregex_iterator sub1(rcb_it, rce_it, sub);
			boost::sregex_iterator sub2;
			std::for_each(sub1,sub2, &regex_callback);
			
			boost::regex teq("([0-9A-Fa-f]+[\s]+TEQ.*$)");
			boost::sregex_iterator teq1(rcb_it, rce_it, teq);
			boost::sregex_iterator teq2;
			std::for_each(teq1,teq2, &regex_callback);
			
			boost::regex tst("([0-9A-Fa-f]+[\s]+TST.*$)");
			boost::sregex_iterator tst1(rcb_it, rce_it, tst);
			boost::sregex_iterator tst2;
			std::for_each(tst1,tst2, &regex_callback);
			
			boost::regex cdp("([0-9A-Fa-f]+[\s]+CDP.*$)");
			boost::sregex_iterator cdp1(rcb_it, rce_it, cdp);
			boost::sregex_iterator cdp2;
			std::for_each(cdp1,cdp2, &regex_callback);
			
			boost::regex pkhbt("([0-9A-Fa-f]+[\s]+PKHBT.*$)");
			boost::sregex_iterator pkhbt1(rcb_it, rce_it, pkhbt);
			boost::sregex_iterator pkhbt2;
			std::for_each(pkhbt1,pkhbt2, &regex_callback);
			
			boost::regex pkhtb("([0-9A-Fa-f]+[\s]+PKHTB.*$)");
			boost::sregex_iterator pkhtb1(rcb_it, rce_it, pkhtb);
			boost::sregex_iterator pkhtb2;
			std::for_each(pkhtb1,pkhtb2, &regex_callback);
			
			boost::regex rev("([0-9A-Fa-f]+[\s]+REV.*$)");
			boost::sregex_iterator rev1(rcb_it, rce_it, rev);
			boost::sregex_iterator rev2;
			std::for_each(rev1,rev2, &regex_callback);
			
			boost::regex rev16("([0-9A-Fa-f]+[\s]+REV16.*$)");
			boost::sregex_iterator rev161(rcb_it, rce_it, rev16);
			boost::sregex_iterator rev162;
			std::for_each(rev161,rev162, &regex_callback);
			
			boost::regex revsh("([0-9A-Fa-f]+[\s]+REVSH.*$)");
			boost::sregex_iterator revsh1(rcb_it, rce_it, revsh);
			boost::sregex_iterator revsh2;
			std::for_each(revsh1,revsh2, &regex_callback);
			
			boost::regex sel("([0-9A-Fa-f]+[\s]+SEL.*$)");
			boost::sregex_iterator sel1(rcb_it, rce_it, sel);
			boost::sregex_iterator sel2;
			std::for_each(sel1,sel2, &regex_callback);
			
			// dump static container content 
			for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
			{
				this->m_DataInstructions.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
			}
			
			// and clear
			generic_op_bucket.clear();
			
			/* Jump ops */
			boost::regex b("([0-9A-Fa-f]+[\s]+B.*$)");
			boost::sregex_iterator b1(rcb_it, rce_it, b);
			boost::sregex_iterator b2;
			std::for_each(b1,b2, &regex_callback);
			
			boost::regex bl("([0-9A-Fa-f]+[\s]+BL.*$)");
			boost::sregex_iterator bl1(rcb_it, rce_it, bl);
			boost::sregex_iterator bl2;
			std::for_each(bl1,bl2, &regex_callback);
			
			boost::regex bx("([0-9A-Fa-f]+[\s]+BX.*$)");
			boost::sregex_iterator bx1(rcb_it, rce_it, bx);
			boost::sregex_iterator bx2;
			std::for_each(bx1,bx2, &regex_callback);
			
			boost::regex blx("([0-9A-Fa-f]+[\s]+BLX.*$)");
			boost::sregex_iterator blx1(rcb_it, rce_it, blx);
			boost::sregex_iterator blx2;
			std::for_each(blx1,blx2, &regex_callback);
			
			boost::regex bxj("([0-9A-Fa-f]+[\s]+BXJ.*$)");
			boost::sregex_iterator bxj1(rcb_it, rce_it, bxj);
			boost::sregex_iterator bxj2;
			std::for_each(bxj1,bxj2, &regex_callback);
			
			// dump static container content 
			for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
			{
				this->m_JumpInstructions.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
			}
			
			// and clear
			generic_op_bucket.clear();
			
			/* Multiply Instructions */
			boost::regex mla("([0-9A-Fa-f]+[\s]+MLA.*$)");
			boost::sregex_iterator mla1(rcb_it, rce_it, mla);
			boost::sregex_iterator mla2;
			std::for_each(mla1,mla2, &regex_callback);
			
			boost::regex mul("([0-9A-Fa-f]+[\s]+MUL.*$)");
			boost::sregex_iterator mul1(rcb_it, rce_it, mul);
			boost::sregex_iterator mul2;
			std::for_each(mul1,mul2, &regex_callback);
			
			boost::regex smla("([0-9A-Fa-f]+[\s]+SMLA.*$)");
			boost::sregex_iterator smla1(rcb_it, rce_it, smla);
			boost::sregex_iterator smla2;
			std::for_each(smla1,smla2, &regex_callback);
			
			boost::regex smlad("([0-9A-Fa-f]+[\s]+SMLAD.*$)");
			boost::sregex_iterator smlad1(rcb_it, rce_it, smlad);
			boost::sregex_iterator smlad2;
			std::for_each(smlad1,smlad2, &regex_callback);
			
			boost::regex smlal("([0-9A-Fa-f]+[\s]+SMLAL.*$)");
			boost::sregex_iterator smlal1(rcb_it, rce_it, smlal);
			boost::sregex_iterator smlal2;
			std::for_each(smlal1,smlal2, &regex_callback);
			
			boost::regex smlald("([0-9A-Fa-f]+[\s]+SMLALD.*$)");
			boost::sregex_iterator smlald1(rcb_it, rce_it, smlald);
			boost::sregex_iterator smlald2;
			std::for_each(smlald1,smlald2, &regex_callback);
			
			boost::regex smlam("([0-9A-Fa-f]+[\s]+SMLAM.*$)");
			boost::sregex_iterator smlam1(rcb_it, rce_it, smlam);
			boost::sregex_iterator smlam2;
			std::for_each(smlam1,smlam2, &regex_callback);
			
			boost::regex smlsd("([0-9A-Fa-f]+[\s]+SMLSD.*$)");
			boost::sregex_iterator smlsd1(rcb_it, rce_it, smlsd);
			boost::sregex_iterator smlsd2;
			std::for_each(smlsd1,smlsd2, &regex_callback);
			
			boost::regex smmla("([0-9A-Fa-f]+[\s]+SMMLA.*$)");
			boost::sregex_iterator smmla1(rcb_it, rce_it, smmla);
			boost::sregex_iterator smmla2;
			std::for_each(smmla1,smmla2, &regex_callback);
			
			boost::regex smmul("([0-9A-Fa-f]+[\s]+SMMUL.*$)");
			boost::sregex_iterator smmul1(rcb_it, rce_it, smmul);
			boost::sregex_iterator smmul2;
			std::for_each(smmul1,smmul2, &regex_callback);
			
			boost::regex smmls("([0-9A-Fa-f]+[\s]+SMMLS.*$)");
			boost::sregex_iterator smmls1(rcb_it, rce_it, smlald);
			boost::sregex_iterator smmls2;
			std::for_each(smmls1,smmls2, &regex_callback);
			
			boost::regex smuad("([0-9A-Fa-f]+[\s]+SMUAD.*$)");
			boost::sregex_iterator smuad1(rcb_it, rce_it, smuad);
			boost::sregex_iterator smuad2;
			std::for_each(smuad1,smuad2, &regex_callback);
			
			boost::regex smul("([0-9A-Fa-f]+[\s]+SMUL.*$)");
			boost::sregex_iterator smul1(rcb_it, rce_it, smul);
			boost::sregex_iterator smul2;
			std::for_each(smul1,smul2, &regex_callback);
			
			boost::regex smull("([0-9A-Fa-f]+[\s]+SMULL.*$)");
			boost::sregex_iterator smull1(rcb_it, rce_it, smull);
			boost::sregex_iterator smull2;
			std::for_each(smull1,smull2, &regex_callback);
			
			boost::regex smulm("([0-9A-Fa-f]+[\s]+SMULM.*$)");
			boost::sregex_iterator smulm1(rcb_it, rce_it, smulm);
			boost::sregex_iterator smulm2;
			std::for_each(smulm1,smulm2, &regex_callback);
			
			boost::regex smusd("([0-9A-Fa-f]+[\s]+SMUSD.*$)");
			boost::sregex_iterator smusd1(rcb_it, rce_it,smusd);
			boost::sregex_iterator smusd2;
			std::for_each(smusd1,smusd2, &regex_callback);
			
			boost::regex umaal("([0-9A-Fa-f]+[\s]+UMAAL.*$)");
			boost::sregex_iterator umaal1(rcb_it, rce_it, umaal);
			boost::sregex_iterator umaal2;
			std::for_each(smlald1,smlald2, &regex_callback);
			
			boost::regex umlal("([0-9A-Fa-f]+[\s]+UMLAL.*$)");
			boost::sregex_iterator umlal1(rcb_it, rce_it, umlal);
			boost::sregex_iterator umlal2;
			std::for_each(umlal1,umlal2, &regex_callback);
			
			boost::regex umull("([0-9A-Fa-f]+[\s]+UMULL.*$)");
			boost::sregex_iterator umull1(rcb_it, rce_it, umull);
			boost::sregex_iterator umull2;
			std::for_each(umull1,umull2, &regex_callback);
			
			// dump static container content 
			for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
			{
				this->m_MultiplyInstructions.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
			}
			
			// and clear
			generic_op_bucket.clear();
			
			/* Parallel Addition */
			boost::regex qadd16("([0-9A-Fa-f]+[\s]+QADD16.*$)");
			boost::sregex_iterator qadd161(rcb_it, rce_it, qadd16);
			boost::sregex_iterator qadd162;
			std::for_each(qadd161,qadd162, &regex_callback);
			
			boost::regex qadd8("([0-9A-Fa-f]+[\s]+QADD8.*$)");
			boost::sregex_iterator qadd81(rcb_it, rce_it, qadd8);
			boost::sregex_iterator qadd82;
			std::for_each(qadd81,qadd82, &regex_callback);
			
			boost::regex qaddsubx("([0-9A-Fa-f]+[\s]+QADDSUBX.*$)");
			boost::sregex_iterator qaddsubx1(rcb_it, rce_it, qaddsubx);
			boost::sregex_iterator qaddsubx2;
			std::for_each(qaddsubx1,qaddsubx2, &regex_callback);
			
			boost::regex qsub16("([0-9A-Fa-f]+[\s]+QSUB16.*$)");
			boost::sregex_iterator qsub161(rcb_it, rce_it, qsub16);
			boost::sregex_iterator qsub162;
			std::for_each(qsub161,qsub162, &regex_callback);
			
			boost::regex qsub8("([0-9A-Fa-f]+[\s]+QSUB8.*$)");
			boost::sregex_iterator qsub81(rcb_it, rce_it, qsub8);
			boost::sregex_iterator qsub82;
			std::for_each(qsub81,qsub82, &regex_callback);
			
			boost::regex qsubaddx("([0-9A-Fa-f]+[\s]+QSUBADDX.*$)");
			boost::sregex_iterator qsubaddx1(rcb_it, rce_it, qsubaddx);
			boost::sregex_iterator qsubaddx2;
			std::for_each(qsubaddx1,qsubaddx2, &regex_callback);
			
			boost::regex sadd16("([0-9A-Fa-f]+[\s]+SADD16.*$)");
			boost::sregex_iterator sadd161(rcb_it, rce_it, sadd16);
			boost::sregex_iterator sadd162;
			std::for_each(sadd161,sadd162, &regex_callback);
			
			boost::regex sadd8("([0-9A-Fa-f]+[\s]+SADD8.*$)");
			boost::sregex_iterator sadd81(rcb_it, rce_it, sadd8);
			boost::sregex_iterator sadd82;
			std::for_each(sadd81,sadd82, &regex_callback);
			
			boost::regex saddsubx("([0-9A-Fa-f]+[\s]+SADDSUBX.*$)");
			boost::sregex_iterator saddsubx1(rcb_it, rce_it, saddsubx);
			boost::sregex_iterator saddsubx2;
			std::for_each(saddsubx1,saddsubx2, &regex_callback);
			
			boost::regex ssub16("([0-9A-Fa-f]+[\s]+SSUB16.*$)");
			boost::sregex_iterator ssub161(rcb_it, rce_it, ssub16);
			boost::sregex_iterator ssub162;
			std::for_each(ssub161,ssub162, &regex_callback);
			
			boost::regex ssub8("([0-9A-Fa-f]+[\s]+SSUB8.*$)");
			boost::sregex_iterator ssub81(rcb_it, rce_it, ssub8);
			boost::sregex_iterator ssub82;
			std::for_each(ssub81,ssub82, &regex_callback);
			
			boost::regex ssubaddx("([0-9A-Fa-f]+[\s]+SSUBADDX.*$)");
			boost::sregex_iterator ssubaddx1(rcb_it, rce_it, ssubaddx);
			boost::sregex_iterator ssubaddx2;
			std::for_each(ssubaddx1,ssubaddx2, &regex_callback);
			
			boost::regex shadd16("([0-9A-Fa-f]+[\s]+SHADD16.*$)");
			boost::sregex_iterator shadd161(rcb_it, rce_it, shadd16);
			boost::sregex_iterator shadd162;
			std::for_each(shadd161,shadd162, &regex_callback);
			
			boost::regex shadd8("([0-9A-Fa-f]+[\s]+SHADD8.*$)");
			boost::sregex_iterator shadd81(rcb_it, rce_it, shadd8);
			boost::sregex_iterator shadd82;
			std::for_each(shadd81,shadd82, &regex_callback);
			
			boost::regex shaddsubx("([0-9A-Fa-f]+[\s]+SHADDSUBX.*$)");
			boost::sregex_iterator shaddsubx1(rcb_it, rce_it, shaddsubx);
			boost::sregex_iterator shaddsubx2;
			std::for_each(shaddsubx1,shaddsubx2, &regex_callback);
			
			boost::regex shsub16("([0-9A-Fa-f]+[\s]+SHSUB16.*$)");
			boost::sregex_iterator shsub161(rcb_it, rce_it, shsub16);
			boost::sregex_iterator shsub162;
			std::for_each(shsub161,shsub162, &regex_callback);
			
			boost::regex shsub8("([0-9A-Fa-f]+[\s]+SHSUB8.*$)");
			boost::sregex_iterator shsub81(rcb_it, rce_it, shsub8);
			boost::sregex_iterator shsub82;
			std::for_each(shsub81,shsub82, &regex_callback);
			
			boost::regex shsubaddx("([0-9A-Fa-f]+[\s]+SHSUBADDX.*$)");
			boost::sregex_iterator shsubaddx1(rcb_it, rce_it, shsubaddx);
			boost::sregex_iterator shsubaddx2;
			std::for_each(shsubaddx1,shsubaddx2, &regex_callback);
			
			boost::regex uadd16("([0-9A-Fa-f]+[\s]+UADD16.*$)");
			boost::sregex_iterator uadd161(rcb_it, rce_it, uadd16);
			boost::sregex_iterator uadd162;
			std::for_each(uadd161,uadd162, &regex_callback);
			
			boost::regex uadd8("([0-9A-Fa-f]+[\s]+UADD8.*$)");
			boost::sregex_iterator uadd81(rcb_it, rce_it, uadd8);
			boost::sregex_iterator uadd82;
			std::for_each(uadd81,uadd82, &regex_callback);
			
			boost::regex uaddsubx("([0-9A-Fa-f]+[\s]+UADDSUBX.*$)");
			boost::sregex_iterator uaddsubx1(rcb_it, rce_it, uaddsubx);
			boost::sregex_iterator uaddsubx2;
			std::for_each(uaddsubx1,uaddsubx2, &regex_callback);
			
			boost::regex usub16("([0-9A-Fa-f]+[\s]+USUB16.*$)");
			boost::sregex_iterator usub161(rcb_it, rce_it, usub16);
			boost::sregex_iterator usub162;
			std::for_each(usub161,usub162, &regex_callback);
			
			boost::regex usub8("([0-9A-Fa-f]+[\s]+USUB8.*$)");
			boost::sregex_iterator usub81(rcb_it, rce_it, usub8);
			boost::sregex_iterator usub82;
			std::for_each(usub81,usub82, &regex_callback);
			
			boost::regex usubaddx("([0-9A-Fa-f]+[\s]+USUBADDX.*$)");
			boost::sregex_iterator usubaddx1(rcb_it, rce_it, usubaddx);
			boost::sregex_iterator usubaddx2;
			std::for_each(usubaddx1,usubaddx2, &regex_callback);
			
			boost::regex uhadd16("([0-9A-Fa-f]+[\s]+UHADD16.*$)");
			boost::sregex_iterator uhadd161(rcb_it, rce_it, uhadd16);
			boost::sregex_iterator uhadd162;
			std::for_each(uhadd161,uhadd162, &regex_callback);
			
			boost::regex uhadd8("([0-9A-Fa-f]+[\s]+UHADD8.*$)");
			boost::sregex_iterator uhadd81(rcb_it, rce_it, uhadd8);
			boost::sregex_iterator uhadd82;
			std::for_each(uhadd81,uhadd82, &regex_callback);
			
			boost::regex uhaddsubx("([0-9A-Fa-f]+[\s]+UHADDSUBX.*$)");
			boost::sregex_iterator uhaddsubx1(rcb_it, rce_it, uhaddsubx);
			boost::sregex_iterator uhaddsubx2;
			std::for_each(uhaddsubx1,uhaddsubx2, &regex_callback);
			
			boost::regex uhsub16("([0-9A-Fa-f]+[\s]+UHSUB16.*$)");
			boost::sregex_iterator uhsub161(rcb_it, rce_it, uhsub16);
			boost::sregex_iterator uhsub162;
			std::for_each(uhsub161,uhsub162, &regex_callback);
			
			boost::regex uhsub8("([0-9A-Fa-f]+[\s]+UHSUB8.*$)");
			boost::sregex_iterator uhsub81(rcb_it, rce_it, uhsub8);
			boost::sregex_iterator uhsub82;
			std::for_each(uhsub81,uhsub82, &regex_callback);
			
			boost::regex uhsubaddx("([0-9A-Fa-f]+[\s]+UHSUBADDX.*$)");
			boost::sregex_iterator uhsubaddx1(rcb_it, rce_it, uhsubaddx);
			boost::sregex_iterator uhsubaddx2;
			std::for_each(uhsubaddx1,uhsubaddx2, &regex_callback);
			
			boost::regex uqadd16("([0-9A-Fa-f]+[\s]+UQADD16.*$)");
			boost::sregex_iterator uqadd161(rcb_it, rce_it, uqadd16);
			boost::sregex_iterator uqadd162;
			std::for_each(uqadd161,uqadd162, &regex_callback);
			
			boost::regex uqadd8("([0-9A-Fa-f]+[\s]+UQADD8.*$)");
			boost::sregex_iterator uqadd81(rcb_it, rce_it, uqadd8);
			boost::sregex_iterator uqadd82;
			std::for_each(uqadd81,uqadd82, &regex_callback);
			
			boost::regex uqaddsubx("([0-9A-Fa-f]+[\s]+UQADDSUBX.*$)");
			boost::sregex_iterator uqaddsubx1(rcb_it, rce_it, uqaddsubx);
			boost::sregex_iterator uqaddsubx2;
			std::for_each(uqaddsubx1,uqaddsubx2, &regex_callback);
			
			boost::regex uqsub16("([0-9A-Fa-f]+[\s]+UQSUB16.*$)");
			boost::sregex_iterator uqsub161(rcb_it, rce_it, uqsub16);
			boost::sregex_iterator uqsub162;
			std::for_each(uqsub161,uqsub162, &regex_callback);
			
			boost::regex uqsub8("([0-9A-Fa-f]+[\s]+UQSUB8.*$)");
			boost::sregex_iterator uqsub81(rcb_it, rce_it, uqsub8);
			boost::sregex_iterator uqsub82;
			std::for_each(uqsub81,uqsub82, &regex_callback);
			
			boost::regex uqsubaddx("([0-9A-Fa-f]+[\s]+UQSUBADDX.*$)");
			boost::sregex_iterator uqsubaddx1(rcb_it, rce_it, uqsubaddx);
			boost::sregex_iterator uqsubaddx2;
			std::for_each(uqsubaddx1,uqsubaddx2, &regex_callback);
			
			// dump static container content 
			for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
			{
				this->m_ArithmeticInstructions.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
			}
			
			// and clear
			generic_op_bucket.clear();
			
			/* Genric Math : sign/ zero extend 	*/	
			boost::regex sxtab16("([0-9A-Fa-f]+[\s]+SXTAB16.*$)");
			boost::sregex_iterator sxtab161(rcb_it, rce_it, sxtab16);
			boost::sregex_iterator sxtab162;
			std::for_each(sxtab161,sxtab162, &regex_callback);
			
			boost::regex sxtab("([0-9A-Fa-f]+[\s]+SXTAB.*$)");
			boost::sregex_iterator sxtab1(rcb_it, rce_it, sxtab);
			boost::sregex_iterator sxtab2;
			std::for_each(sxtab1,sxtab2, &regex_callback);
			
			boost::regex sxtah("([0-9A-Fa-f]+[\s]+SXTAH.*$)");
			boost::sregex_iterator sxtah1(rcb_it, rce_it, sxtah);
			boost::sregex_iterator sxtah2;
			std::for_each(sxtah1,sxtah2, &regex_callback);
			
			boost::regex sxtb16("([0-9A-Fa-f]+[\s]+SXTB16.*$)");
			boost::sregex_iterator sxtb161(rcb_it, rce_it, sxtb16);
			boost::sregex_iterator sxtb162;
			std::for_each(sxtb161,sxtb162, &regex_callback);
			
			boost::regex sxtb("([0-9A-Fa-f]+[\s]+SXTB.*$)");
			boost::sregex_iterator sxtb1(rcb_it, rce_it, sxtb);
			boost::sregex_iterator sxtb2;
			std::for_each(sxtb1,sxtb2, &regex_callback);
			
			boost::regex sxth("([0-9A-Fa-f]+[\s]+SXTH.*$)");
			boost::sregex_iterator sxth1(rcb_it, rce_it, sxth);
			boost::sregex_iterator sxth2;
			std::for_each(sxth1,sxth2, &regex_callback);
			
			boost::regex uxtab16("([0-9A-Fa-f]+[\s]+UXTAB16.*$)");
			boost::sregex_iterator uxtab161(rcb_it, rce_it, uxtab16);
			boost::sregex_iterator uxtab162;
			std::for_each(uxtab161,uxtab162, &regex_callback);
			
			boost::regex uxtab("([0-9A-Fa-f]+[\s]+UXTAB.*$)");
			boost::sregex_iterator uxtab1(rcb_it, rce_it, uxtab);
			boost::sregex_iterator uxtab2;
			std::for_each(uxtab1,uxtab2, &regex_callback);
			
			boost::regex uxtah("([0-9A-Fa-f]+[\s]+UXTAH.*$)");
			boost::sregex_iterator uxtah1(rcb_it, rce_it, uxtah);
			boost::sregex_iterator uxtah2;
			std::for_each(uxtah1,uxtah2, &regex_callback);
			
			boost::regex uxtb16("([0-9A-Fa-f]+[\s]+UXTB16.*$)");
			boost::sregex_iterator uxtb161(rcb_it, rce_it, uxtb16);
			boost::sregex_iterator uxtb162;
			std::for_each(uxtb161,uxtb162, &regex_callback);
			
			boost::regex uxtb("([0-9A-Fa-f]+[\s]+UXTB.*$)");
			boost::sregex_iterator uxtb1(rcb_it, rce_it, uxtb);
			boost::sregex_iterator uxtb2;
			std::for_each(uxtb1,uxtb2, &regex_callback);
			
			boost::regex uxth("([0-9A-Fa-f]+[\s]+UXTH.*$)");
			boost::sregex_iterator uxth1(rcb_it, rce_it, uxth);
			boost::sregex_iterator uxth2;
			std::for_each(uxth1,uxth2, &regex_callback);
			
			boost::regex clz("([0-9A-Fa-f]+[\s]+CLZ.*$)");
			boost::sregex_iterator clz1(rcb_it, rce_it, clz);
			boost::sregex_iterator clz2;
			std::for_each(clz1,clz2, &regex_callback);
			
			boost::regex ssat("([0-9A-Fa-f]+[\s]+SSAT.*$)");
			boost::sregex_iterator ssat1(rcb_it, rce_it, ssat);
			boost::sregex_iterator ssat2;
			std::for_each(ssat1,ssat2, &regex_callback);
			
			boost::regex ssat16("([0-9A-Fa-f]+[\s]+SSAT16.*$)");
			boost::sregex_iterator ssat161(rcb_it, rce_it, ssat16);
			boost::sregex_iterator ssat162;
			std::for_each(ssat161,ssat162, &regex_callback);
			
			boost::regex usat("([0-9A-Fa-f]+[\s]+USAT.*$)");
			boost::sregex_iterator usat1(rcb_it, rce_it, usat);
			boost::sregex_iterator usat2;
			std::for_each(usat1,usat2, &regex_callback);
			
			boost::regex usat16("([0-9A-Fa-f]+[\s]+USAT16.*$)");
			boost::sregex_iterator usat161(rcb_it, rce_it, usat16);
			boost::sregex_iterator usat162;
			std::for_each(usat161,usat162, &regex_callback);
			
			// dump static container content 
			for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
			{
				this->m_GenericMathInstructions.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
			}
			
			// and clear
			generic_op_bucket.clear();
			
			/* Registers : Status , Load , Store & Semaphore */
			boost::regex mlrs("([0-9A-Fa-f]+[\s]+MLRS.*$)");
			boost::sregex_iterator mlrs1(rcb_it, rce_it, mlrs);
			boost::sregex_iterator mlrs2;
			std::for_each(mlrs1,mlrs2, &regex_callback);
			
			boost::regex msr("([0-9A-Fa-f]+[\s]+MSR.*$)");
			boost::sregex_iterator msr1(rcb_it, rce_it, msr);
			boost::sregex_iterator msr2;
			std::for_each(msr1,msr2, &regex_callback);
			
			boost::regex cps("([0-9A-Fa-f]+[\s]+CPS.*$)");
			boost::sregex_iterator cps1(rcb_it, rce_it, cps);
			boost::sregex_iterator cps2;
			std::for_each(cps1,cps2, &regex_callback);
			
			boost::regex setend("([0-9A-Fa-f]+[\s]+SETEND.*$)");
			boost::sregex_iterator setend1(rcb_it, rce_it, setend);
			boost::sregex_iterator setend2;
			std::for_each(setend1,setend2, &regex_callback);
			
			boost::regex ldr("([0-9A-Fa-f]+[\s]+LDR.*$)");
			boost::sregex_iterator ldr1(rcb_it, rce_it, ldr);
			boost::sregex_iterator ldr2;
			std::for_each(ldr1,ldr2, &regex_callback);
			
			boost::regex ldrb("([0-9A-Fa-f]+[\s]+LDRB.*$)");
			boost::sregex_iterator ldrb1(rcb_it, rce_it, ldrb);
			boost::sregex_iterator ldrb2;
			std::for_each(ldrb1,ldrb2, &regex_callback);
			
			boost::regex ldrbt("([0-9A-Fa-f]+[\s]+LDRBT.*$)");
			boost::sregex_iterator ldrbt1(rcb_it, rce_it, ldrbt);
			boost::sregex_iterator ldrbt2;
			std::for_each(ldrbt1,ldrbt2, &regex_callback);
			
			boost::regex ldrd("([0-9A-Fa-f]+[\s]+LDRD.*$)");
			boost::sregex_iterator ldrd1(rcb_it, rce_it, ldrd);
			boost::sregex_iterator ldrd2;
			std::for_each(ldrd1,ldrd2, &regex_callback);
			
			boost::regex ldrex("([0-9A-Fa-f]+[\s]+LDREX.*$)");
			boost::sregex_iterator ldrex1(rcb_it, rce_it, ldrex);
			boost::sregex_iterator ldrex2;
			std::for_each(ldrex1,ldrex2, &regex_callback);
			
			boost::regex ldrh("([0-9A-Fa-f]+[\s]+LDRH.*$)");
			boost::sregex_iterator ldrh1(rcb_it, rce_it, ldrh);
			boost::sregex_iterator ldrh2;
			std::for_each(ldrh1,ldrh2, &regex_callback);
			
			boost::regex ldrsb("([0-9A-Fa-f]+[\s]+LDRSB.*$)");
			boost::sregex_iterator ldrsb1(rcb_it, rce_it, ldrsb);
			boost::sregex_iterator ldrsb2;
			std::for_each(ldrsb1,ldrsb2, &regex_callback);
			
			boost::regex ldrsh("([0-9A-Fa-f]+[\s]+LDRSH.*$)");
			boost::sregex_iterator ldrsh1(rcb_it, rce_it, ldrsh);
			boost::sregex_iterator ldrsh2;
			std::for_each(ldrsh1,ldrsh2, &regex_callback);
			
			boost::regex ldrt("([0-9A-Fa-f]+[\s]+LDRT.*$)");
			boost::sregex_iterator ldrt1(rcb_it, rce_it, ldrt);
			boost::sregex_iterator ldrt2;
			std::for_each(ldrt1,ldrt2, &regex_callback);
			
			boost::regex str("([0-9A-Fa-f]+[\s]+STR.*$)");
			boost::sregex_iterator str1(rcb_it, rce_it, str);
			boost::sregex_iterator str2;
			std::for_each(str1,str2, &regex_callback);
			
			boost::regex strb("([0-9A-Fa-f]+[\s]+STRB.*$)");
			boost::sregex_iterator strb1(rcb_it, rce_it, strb);
			boost::sregex_iterator strb2;
			std::for_each(strb1,strb2, &regex_callback);
			
			boost::regex strbt("([0-9A-Fa-f]+[\s]+STRBT.*$)");
			boost::sregex_iterator strbt1(rcb_it, rce_it, strbt);
			boost::sregex_iterator strbt2;
			std::for_each(strbt1,strbt2, &regex_callback);
			
			boost::regex strd("([0-9A-Fa-f]+[\s]+STRD.*$)");
			boost::sregex_iterator strd1(rcb_it, rce_it, strd);
			boost::sregex_iterator strd2;
			std::for_each(strd1,strd2, &regex_callback);
			
			boost::regex strex("([0-9A-Fa-f]+[\s]+STREX.*$)");
			boost::sregex_iterator strex1(rcb_it, rce_it, strex);
			boost::sregex_iterator strex2;
			std::for_each(strex1,strex2, &regex_callback);
			
			boost::regex strh("([0-9A-Fa-f]+[\s]+STRH.*$)");
			boost::sregex_iterator strh1(rcb_it, rce_it, strh);
			boost::sregex_iterator strh2;
			std::for_each(strh1,strh2, &regex_callback);
			
			boost::regex strt("([0-9A-Fa-f]+[\s]+STRT.*$)");
			boost::sregex_iterator strt1(rcb_it, rce_it, strt);
			boost::sregex_iterator strt2;
			std::for_each(strt1,strt2, &regex_callback);
			
			boost::regex ldm("([0-9A-Fa-f]+[\s]+LDM.*$)");
			boost::sregex_iterator ldm1(rcb_it, rce_it, ldm);
			boost::sregex_iterator ldm2;
			std::for_each(ldm1,ldm2, &regex_callback);
			
			boost::regex stm("([0-9A-Fa-f]+[\s]+STM.*$)");
			boost::sregex_iterator stm1(rcb_it, rce_it, stm);
			boost::sregex_iterator stm2;
			std::for_each(stm1,stm2, &regex_callback);
			
			boost::regex smp("([0-9A-Fa-f]+[\s]+SMP.*$)");
			boost::sregex_iterator smp1(rcb_it, rce_it, smp);
			boost::sregex_iterator smp2;
			std::for_each(smp1,smp2, &regex_callback);
			
			boost::regex smpb("([0-9A-Fa-f]+[\s]+SMPB.*$)");
			boost::sregex_iterator smpb1(rcb_it, rce_it, smpb);
			boost::sregex_iterator smpb2;
			std::for_each(smpb1,smpb2, &regex_callback);
			
			boost::regex ldc("([0-9A-Fa-f]+[\s]+LDC.*$)");
			boost::sregex_iterator ldc1(rcb_it, rce_it, ldc);
			boost::sregex_iterator ldc2;
			std::for_each(ldc1,ldc2, &regex_callback);
			
			boost::regex mcr("([0-9A-Fa-f]+[\s]+MCR.*$)");
			boost::sregex_iterator mcr1(rcb_it, rce_it, mcr);
			boost::sregex_iterator mcr2;
			std::for_each(mcr1,mcr2, &regex_callback);
			
			boost::regex mcrr("([0-9A-Fa-f]+[\s]+MCRR.*$)");
			boost::sregex_iterator mcrr1(rcb_it, rce_it, mcrr);
			boost::sregex_iterator mcrr2;
			std::for_each(mcrr1,mcrr2, &regex_callback);
			
			boost::regex mrc("([0-9A-Fa-f]+[\s]+MRC.*$)");
			boost::sregex_iterator mrc1(rcb_it, rce_it, mrc);
			boost::sregex_iterator mrc2;
			std::for_each(mrc1,mrc2, &regex_callback);
			
			boost::regex mrcc("([0-9A-Fa-f]+[\s]+MRCC.*$)");
			boost::sregex_iterator mrcc1(rcb_it, rce_it, mrcc);
			boost::sregex_iterator mrcc2;
			std::for_each(mrcc1,mrcc2, &regex_callback);
			
			boost::regex stc("([0-9A-Fa-f]+[\s]+STC.*$)");
			boost::sregex_iterator stc1(rcb_it, rce_it, stc);
			boost::sregex_iterator stc2;
			std::for_each(stc1,stc2, &regex_callback);
			
			// dump static container content 
			for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
			{
			
				this->m_RegisterInstructions.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
			}
			
			// and clear
			generic_op_bucket.clear();
			
			/* Exceptions */
			boost::regex bkpt("([0-9A-Fa-f]+[\s]+BKPT.*$)");
			boost::sregex_iterator bkpt1(rcb_it, rce_it, bkpt);
			boost::sregex_iterator bkpt2;
			std::for_each(bkpt1,bkpt2, &regex_callback);
			
			boost::regex swi("([0-9A-Fa-f]+[\s]+SWI.*$)");
			boost::sregex_iterator swi1(rcb_it, rce_it, swi);
			boost::sregex_iterator swi2;
			std::for_each(swi1,swi2, &regex_callback);
			
			// dump static container content 
			for(op_cat_it=generic_op_bucket.begin() ; op_cat_it != generic_op_bucket.end(); op_cat_it++ )
			{
				this->m_ExcepInstructions.insert(pair<std::string,std::string>(op_cat_it->first, op_cat_it->second));
			}
			
			// and clear
			generic_op_bucket.clear();
			 
		}
	}
	catch(exception& e)
	{
		if(this->m_debug)
		{
			printf("[ERROR] problems creating instruction profile:%s\n",e.what());
		}
	}
	
	return profileCreated;
}

bool APTool::SaveInstructionProfileToDB()
{
	return false;
}

bool APTool::DumpAppLogicXml()
{
	bool logicDumped = false;
	int rc;                 
	xmlTextWriterPtr writer;
	xmlChar * tmp;
	
	try
	{
		// Create xmlWriter with no compression
		writer = xmlNewTextWriterFilename("/Users/administrator/Dev/App_Recorder/Data/Out/Semantic/Processor/TouchCalc_Proc.xml",0);
		
		if(writer == NULL)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems creating the xml writer!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems creating xml writer!");
		}
		
		
		// Start Doc
		rc = xmlTextWriterStartDocument(writer, NULL, MY_ENCODING, NULL);
		
		if(rc < 0)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems creating the xml document!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems creating xml document!");
		}
		
		// First/Root Element
		rc = xmlTextWriterStartElement(writer, BAD_CAST "Operations");
		
		if(rc < 0)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems creating the xml element!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems creating xml element!");
		}
		
		// Close xml Root
		rc = xmlTextWriterEndElement(writer);
		
		if(rc < 0)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems ending the xml element!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems ending xml element!");
		}
		
		// Close xml Doc alltogether
		rc = xmlTextWriterEndDocument(writer);
		
		if(rc < 0)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems ending the xml document!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems ending xml document!");
		}
		
		xmlFreeTextWriter(writer);
		
		logicDumped = true;
		
		
	}
	catch(exception& e)
	{
		if(this->m_debug)
		{
			printf("[ERROR] dumping the proc xml file to disk!:%s\n", e.what());
		}
		
	}
	
	return logicDumped;
}

bool APTool::DumpStaticAppDataXml()
{
	bool logicDumped = false;
	int rc;                 
	xmlTextWriterPtr writer;
	xmlChar * tmp;
	
	try
	{
		// Create xmlWriter with no compression
		writer = xmlNewTextWriterFilename("/Users/administrator/Dev/App_Recorder/Data/Out/Semantic/Addresses/TouchCalc_Addr.xml",0);
		
		if(writer == NULL)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems creating the xml writer!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems creating xml writer!");
		}
		
		
		// Start Doc
		rc = xmlTextWriterStartDocument(writer, NULL, MY_ENCODING, NULL);
		
		if(rc < 0)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems creating the xml document!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems creating xml document!");
		}
		
		// First/Root Element
		rc = xmlTextWriterStartElement(writer, BAD_CAST "Addresses");
		
		if(rc < 0)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems creating the xml element!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems creating xml element!");
		}
		
		// Close xml Root
		rc = xmlTextWriterEndElement(writer);
		
		if(rc < 0)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems ending the xml element!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems ending xml element!");
		}
		
		// Close xml Doc alltogether
		rc = xmlTextWriterEndDocument(writer);
		
		if(rc < 0)
		{
			if(this->m_debug)
			{
				printf("[ERROR] problems ending the xml document!\n");
			}
			
			throw new std::runtime_error("[ERROR] problems ending xml document!");
		}
		
		xmlFreeTextWriter(writer);
		
		logicDumped = true;
		
		
	}
	catch(exception& e)
	{
		if(this->m_debug)
		{
			printf("[ERROR] dumping the static data xml file to disk!:%s\n", e.what());
		}
		
	}
	
	return logicDumped;
}


void APTool::SetClientKernel(std::string ck_v)
{
	KernelType kt;
	
	if(strcmp(ck_v.c_str(),"XNU")==0)
	{
		kt = XNU;
	}
	
	if(strcmp(ck_v.c_str(),"MACH")==0)
	{
		kt = MACH;
	}
	
	this->m_kernel = kt;
}

void APTool::SetClientArch(std::string a)
{
	ArchType at;
	
	if(strcmp(a.c_str(),"ARMv6")==0)
	{
		at = ARMv6;
	}
	
	this->m_arch = at;
	
}

void APTool::SetClientOS(std::string os)
{
	OSType ost;
	
	if(strcmp(os.c_str(),"iPhoneOS_3_0")==0)
	{
		ost = iPhoneOS_3_0;
	}
	
	if(strcmp(os.c_str(),"iPhoneOS_3_1")==0)
	{
		ost = iPhoneOS_3_1;
	}
	
	if(strcmp(os.c_str(),"OSx_10_5_6")==0)
	{
		ost = OSx_10_5_6;
	}
	
	this->m_clientOS = ost;
}

void APTool::SetClientExFormat(std::string ef)
{
	ExFormat exe;
	
	if(strcmp(ef.c_str(),"PE")==0)
	{
		exe = PE;
	}
	
	if(strcmp(ef.c_str(),"MACHO")==0)
	{
		exe = MACHO;
	}
	
	if(strcmp(ef.c_str(),"ELF")==0)
	{
		exe = ELF;
	}
	
	this->m_exe_format = exe;
}

void APTool::SetDynamicProfileEnvironment(std::string dld_dir, std::string in_dir, std::string out_dir, std::string arch_dir)
{
	this->dld_data = dld_dir.c_str();
	this->in_data = in_dir;
	this->out_data = out_dir;
	this->arch_data = arch_dir;
}

void APTool::SetHostKernelProfiler(std::string kprofiler, std::string kprof_scripts, std::string kprof_loc)
{
	this->kprof = kprofiler;
	this->kprofScripts = kprof_scripts;
	this->kprofLoc = kprof_loc;
}

bool APTool::CreateAppProfileConfig(std::string Emulator, std::string Simulator, std::string pkg_name, std::string app_name)
{
	bool profileCreated = false;
	
	try
	{
		this->m_apc_settings.kT = this->m_kernel;
		this->m_apc_settings.aT = this->m_arch;
		this->m_apc_settings.oS = this->m_clientOS;
		this->m_apc_settings.fT = this->m_exe_format;
	
		this->m_apc_settings.app_dir = this->dld_data;
		
		this->m_apc_settings.pkg_name = pkg_name.c_str();
		
		
		this->m_apc_settings.app_name = app_name.c_str();
		
		this->m_apc_settings.kernel_profiler = this->kprof;
		this->m_apc_settings.usesKernelProfiler = true;
		
	
		if(Emulator.length() >0)
		{
			this->m_apc_settings.emulator = Emulator.c_str();
			this->m_apc_settings.usesEmulator = true;
		}
	
		if(Simulator.length() >0)
		{
			this->m_apc_settings.simulator = Simulator.c_str();
			this->m_apc_settings.usesSimulator = true;
		}
		
		profileCreated = true;
	}
	catch(exception& ex)
	{
		if(this->m_debug)
		{
			printf("[ERROR] problems creating AppProfile:%s",ex.what());
		}
	}
	
	return profileCreated;
	
}

void APTool::SetRomInfoData(std::string rom_file)
{
	
}

int main (int argc, char * const argv[])
{
	APTool::DEFAULT_DB db;
	
	std::map<std::string, APTool::DEFAULT_DB> defaultDb;
	
	// Set default directory locations
	std::string download_dir = "./Download";
	std::string resource_in_dir = "./In";
	std::string resource_out_dir = "./Out";
	std::string arch_dir = "./In/Static";
	
	// Set default file/folder locations for logfiles
	std::string in_file = "./in.log";
	std::string out_file = "./out.log";
	std::string err_file = "./err.log";
	std::string rom_file = "./ROM_Info.cfg";
	
	// Set Emulator/Simulator Settings
	std::string emulatorType = "skyEye";
	std::string simulatorType = ""; //"iPhone";
	std::string emulator_loc = "./";
	std::string simulator_loc = ""; //"./";
	
	// Kernel Settings
	std::string kprofiler = "dtrace";
	std::string kprofiler_location = "/usr/sbin";
	std::string kprofiler_scripts = "./Scripts";
	
	// Client Env Settings
	std::string client_arch = "ARMv6";
	std::string client_kernel = "MACH";
	std::string client_exec = "MACHO";
	std::string client_os = "iPhoneOS_3_0";
	
	// Host Env Settings
	std::string host_arch = "x86";
	std::string host_kernel = "XNU";
	std::string host_exec = "MACHO";
	std::string host_os = "OSx_10_5_6";

	// Set default db connect info for registry mapping
	db.name = "IslandData";
	db.host = "localhost";
	db.user = "IslandCoreUser";
	db.pass = "islandWorkStation";
	
	// Set application settings
	std::string app_dld = "TouchCalc.ipa";
	std::string app_pkg = "TouchCalc.app";
	std::string app_bin = "TouchCalc";
	
	// Set default app flags
	bool run_in_foreground = false;
	bool debug = false;
	bool regex_instructions = false;
	bool regex_libraries = false;
	bool regex_native_functions = false;
	bool regex_api_functions = false;
	bool load_custom_config = false;
	bool load_custom_emulator = false;
	bool load_custom_simulator = false;
	bool load_custom_kernel_profiler = false;
	bool use_emulator = true;
	bool use_simulator = false;
	bool use_kernel_profiler = true;
	
	int loop_count;
	int c;
	
	opterr = 0;
	
	if(argc == 1)
	{
		usage();
	}
	
	while((c = getopt(argc, argv, "fdilnacesk")) != -1)
	{
		switch(c)
		{
			case 'f':
				run_in_foreground = true;
				break;
			case 'd':
				run_in_foreground = true;
				debug = true;
				break;
			case 'i':
				regex_instructions = true;
				break;
			case 'l':
				regex_libraries	 = true;
				break;
			case 'n':
				regex_native_functions = true;
				break;
			case 'a':
				regex_api_functions= true;
				break;
			case 'c':
				load_custom_config = true;
				break;
			case 'e':
				load_custom_emulator = true;
				use_emulator = true;
				break;
			case 's':
				load_custom_simulator = true;
				use_simulator = true;
				break;
			case 'k':
				load_custom_kernel_profiler = true;
				use_kernel_profiler = true;
				break;
		    case '?':
				printf("Unrecognized option '%c'.\n", optopt);
				return 1;
				
			default:
				usage();
		}
	}
	
	/* set execution environment flags if they're not already set*/
	if(!use_emulator)
	{
		/*
		 if(strcmp(totalString(vm["use_arch_Emulator"].as<vector<string> >()).c_str(),"yes")==0)
		 {
		 use_emulator = true;
		 printf("We are going to use the emulator!\n");
		 }
		 else
		 */
		{
			printf("Config use-emulator <> yes\n");
		}
		
		
	}
	else
	{
		printf("We are going to use the emulator!\n");
	}
	
	/* set execution environment flags if they're not already set*/
	if(!use_simulator)
	{
		/*
		 if(strcmp(totalString(vm["use_arch_Simmulator"].as<vector<string> >()).c_str(),"yes")==0)
		 {
		 use_emulator = true;
		 printf("We are going to use the simulator!\n");
		 }
		 else
		 */
		{
			printf("Config use-simulator <> yes\n");
		}
		
		
	}
	else
	{
		printf("We are going to use the simulator!\n");
	}

	/* set debug if it isn't set already */
	if(!debug)
	{
		/*
		 if(strcmp(totalString(vm["debug"].as<vector<string> >()).c_str(),"yes") == 0)
		 {
		 run_in_foreground = true;
		 printf("We are debugging!\n");
		 }
		 else
		 */
		{
			printf("Config debug <> yes\n");
		}
		
		
	}
	else
	{
		printf("We debugging!\n");
		
	}
	
	if(!run_in_foreground)
	{
		/*
		 if(strcmp(totalString(vm["runInForground"].as<vector<string> >()).c_str(),"yes") == 0)
		 {
		 run_in_foreground = true;
		 printf("We are running in the foreground!\n");
		 }
		 else
		 */
		{
			printf("Config runInForeground <> yes\n");
		}
		
		
	}
	else
	{
		printf("We started running in the foreground!\n");
		
	}

	if(debug)
	{
		printf("Name:%s\n",db.name.c_str());
		printf("Host:%s\n",db.host.c_str());
		printf("User:%s\n",db.user.c_str());
		printf("Pass:%s\n",db.pass.c_str());
	}
	
	
	try
	{
		// Initialize Objects and Set Flags
		APTool * aP_t = new APTool();
		// Set all the necessary flags
		aP_t->SetDebug(debug);
		
		// Set Profile Config info
		aP_t->SetClientKernel(client_kernel);
		aP_t->SetClientArch(client_arch);
		aP_t->SetClientOS(client_os);
		aP_t->SetClientExFormat(client_exec);
		aP_t->SetDynamicProfileEnvironment(download_dir, resource_in_dir, resource_out_dir, arch_dir);
		aP_t->SetHostKernelProfiler(kprofiler, kprofiler_scripts, kprofiler_location);
				
		// Set App Profile Config
		aP_t->CreateAppProfileConfig(emulatorType, simulatorType, app_pkg, app_bin);
	
		//aP_t->SetRomInfoData(rom_file);
		/*
		// Update Config w/ ROM(s) static for now
		aP_t->LoadAppROM("/Users/administrator/Dev/App_Recorder/Data/In/Static/Instructions/ARM/Unsecured/Verbose/NoDrmRom.asm", "0x0000", "0x0768C", 2000);
	
		if(aP_t->CreateInstructionProfile())
		{
			// worked!
			if(debug)
			{
				printf("Successfully Created Instruction Profile\n");
			}
		}
		*/
		// dump the xml?
		aP_t->DumpAppLogicXml();
		
		// Update Config w/ ROM(s) static for now
		aP_t->LoadAppROM("/Users/administrator/Dev/App_Recorder/Data/In/Static/Instructions/ARM/Unsecured/Verbose/NoDrmRomCString.asm", "0x0000", "0x0768C", 2000);
		
		if(aP_t->CreateStaticDataProfile())
		{
			// worked!
			if(debug)
			{
				printf("Successfully Created Static Data Profile\n");
			}
		}
		
		// dump the xml?
		aP_t->DumpStaticAppDataXml();
	}
	catch(exception& ex)
	{
		if(debug&&run_in_foreground)
		{
			cout<< ex.what() << "\n";
		}
		
		if(!run_in_foreground)
		{
			syslog(LOG_ERR,"%s\n", ex.what());
		}
	}
	
	
    return 0;
}


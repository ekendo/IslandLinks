/*
 *  APTool.h
 *  APTool
 *
 *  Created by Administrator on 11/4/09.
 *  Copyright 2009 EKenDo, LLC. All rights reserved.
 *
 */
// database headers
#include <mysql++.h>

// c++ system headers
#include <iostream>
#include <iomanip>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/filio.h>
#include <sys/uio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/resource.h>
#include <netdb.h>
#include <sys/param.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <stdlib.h>
#include <stdbool.h>
#include <stdexcept>
#include <fcntl.h>
#include <errno.h>
#include <unistd.h>
#include <syslog.h>
#include <string.h>
#include <ctype.h>
#include <ctime>

// boost libraries
#include <boost/version.hpp>
#include <boost/program_options.hpp>
#include <boost/program_options/variables_map.hpp>
#include <boost/program_options/options_description.hpp>
#include <boost/regex.hpp>
#include <boost/filesystem/operations.hpp>
#include <boost/filesystem/path.hpp>

// libxml2 libraries
#include <libxml/encoding.h>
#include <libxml/xmlwriter.h>
#define MY_ENCODING "ISO-8859-1"

// boost namespaces
//namespace po = boost::program_options;
namespace bf = boost::filesystem;

// random utilities
#include <fstream>
#include <iterator>

// other namespaces
using namespace std;

// classes
class APTool
{
public:
	
	enum ArchType
	{
		K68,
		X86,
		DECALPHA,
		BLACKFIN,
		MICROBLAZE,
		XTENSA,
		M86K,
		PARISC, // HP
		ARMv5,
		ARMv6,
		ARMv7,
		IA64,
		MIPS,
		PPC,
		PPC970,
		SPARC,
		I960,
		ITANIUM
	};
	
	enum OSType
	{
		BeOS,
		AmigaOS,
		Inferno,
		BBerryOS,
		SymbianOS,
		Android,
		GarnetOS,
		Solaris,
		OSx_10_5_6,
		iPhoneOS_2_1,
		iPhoneOS_3_0,
		iPhoneOS_3_1,
		WinNT,
		WinCE,
		WinXP,
		WinV,
		Win7,
		apLinux,
		ucLINUX,
		rtLinux,
		fedoraOS_8,
		fedoraOS_9,
		fedoraOS_10,
		fedoraOS_11,
		CEntOS,
		Mandriva,
		Unix,
		SunOS,
		WebOS,
		Plan9,
		Minix
		
	};
	
	enum KernelType
	{
		L4,
		NT,
		CE,
		XNU,
		_BSD, // FreeBSD, OpenBSD, NetBSD, DragonFlyBSD
		GMACH, // GNU
		MACH,	// Carnagie Mellon
		AE, // Amiga's exec.library
		AE2, // Amiga's exec.library SG
		SUNOS,
		SOLARIS,
		HURD, // GNU
		MINIX3,
		LINUX,
		MKLINUX,
		TRIX,
		PLAN9,
		INFERNO,
		NETWARE,
		ROCKBOX,
		SPARTAN
	};
	
	enum ExFormat
	{
		PE,
		NLM,
		ADOTOUT,
		COFF,
		ECOFF,
		XCOFF,
		ELF,
		SUPERH,
		HUNK,
		MACHO
	};
	
	enum ImgType
	{
		PNG,
		BMP,
		GIF,
		TIF,
		JPG,
		DM3,
		DMF3,
		ABM,
		AI,
		ART,
		BLEND,
		BR5,
		CDR,
		CPT,
		CR2,
		DGN,
		DNG,
		DRW,
		DWG,
		DXF,
		EPS,
		EXIF,
		FPF,
		FXG,
		HDP,
		INDD,
		INX,
		JPX,
		MA,
		MAX,
		MB,
		MDI,
		MNG,
		OBJ,
		PCT,
		PDF,
		PPM,
		PS0,
		PSD,
		QXD,
		QXP,
		SVG,
		THM
	};
	
	enum VidType
	{
		MOV,
		MP4,
		GP3,
		G23,
		ASF,
		ASX,
		AVI,
		DIVX,
		DVRMS,
		F4V,
		FBR,
		FLV,
		MKV,
		MPG,
		MTS,
		OGM,
		QT,
		RCPROJECT,
		RM,
		RMVB,
		SMIL,
		SRT,
		STX,
		SWF,
		TS,
		VOB,
		WMV,
		XVID
	};
	
	enum AudType
	{
		AAC,
		AA3,
		AIF,
		AIFF,
		CPR,
		FLAC,
		IFF,
		M3U,
		M4A,
		M4B,
		M4R,
		MID,
		MIDI,
		MOD,
		MP3,
		MPA,
		OGG,
		PTF,
		RA,
		RAM,
		SIB,
		SND,
		WAV,
		WMA
	};
	
	enum ResType
	{
		Audio,
		Video,
		Image
	};
	
	
	enum DBType
	{
		MySQL,
		MsSQK,
		Oracle,
		PostGres
	};
	
	// structs
	struct RES_INFO
	{
		std::string fileName;
		std::string filePath;
		std::string regName;
		APTool::VidType fileVidType;
		APTool::AudType fileAudType;
		APTool::ImgType fileImgType;
		APTool::ResType	fileType;
		bool isInDir;
		bool isIcon;
		bool isSprite;
		bool isButton;
		bool isBackground;
	};
	
	struct APP_ROM_INFO
	{
		APTool::ExFormat fT;
		std::string sectionT;
		std::string offset;
		std::string filePath;
	};
	
	struct APP_PROFILE_CONFIG
	{
		vector<APP_ROM_INFO *> a_r_info;
		vector<RES_INFO> r_info;
		APTool::KernelType kT;
		APTool::ArchType aT;
		APTool::OSType oS;
		APTool::ExFormat fT;
		string startAddress;
		string emulator;
		string simulator;
		string kernel_profiler;
		string pkg_name;
		string app_name;
		string app_dir;
		bool usesEmulator;
		bool usesSimulator;
		bool usesKernelProfiler;
		
	};
	
	
	struct DEFAULT_DB
	{
		string host;
		string name;
		string user;
		string pass;
		APTool::DBType type;
		string kbase;
		
	};
	
	/* Constructors */
	APTool()
	{
		
		m_debug = true;
		m_daemonValue = false;
		m_ipa_Unzipped = false;
		m_app_Unzipped = false;
		m_clientOS = iPhoneOS_3_0;
		m_arch = ARMv6;
		//m_default_Map_DB = MySQL;
	}
	
	/*
	 There will be at least one db connection that this profiler will
	 be pointing to this function will connect and maintain the
	 connection using the DEFAULT_DB structure and the member variable
	 m_currentDefaultDB. It will also handle entries to the syslog
	 in daemon Mode.
	 */
	bool ConnectToDefaultDB(DEFAULT_DB d);
	
	/*
	 There will be at least one db connection that this profiler will
	 be pointing to this function will connect and maintain the
	 connection using the DEFAULT_DB structure and the member variable
	 m_currentDefaultDB. It will also handle entries to the syslog
	 in daemon Mode.
	 */
	bool ConnectToDefaultDB();
	
	/*
	 Create the default profile config to be able to create mappings for the app
	 categories and profiles later. RIght now there are 4 basic profiles that each 
	 have thier own individual mapping.
	 */
	bool CreateAppProfileConfig(std::string emu, std::string simu, std::string pkg, std::string app);
	
	/*
	 Pop the most recent rom out of the local vector and 
	 */
	bool CreateInstructionProfile();
	
	/*
	 Pop the most recent rom out of the local vector and 
	 */
	bool CreateStaticDataProfile();
	
	/*
	 Load the ROM file/handle into memory and set up the APP_ROM_INFO object.
	 */
	bool LoadAppROM(std::string rom_loc, std::string start_address, std::string end_address, int offset);
	
	bool SaveInstructionProfileToDB();
	
	bool DumpAppLogicXml();
	
	bool DumpStaticAppDataXml();
	
	void SetDebug(bool d);
	
	void SetClientKernel(std::string ck_v);
	
	void SetClientArch(std::string ca_v);
	
	void SetClientOS(std::string cOS_v);
	
	void SetClientExFormat(std::string cef_v);
	
	void SetDynamicProfileEnvironment(std::string download, std::string in_dir, std::string out_dir, std::string arch);
	
	void SetHostKernelProfiler(std::string kprof, std::string kprof_scripts, std::string kprof_loc);
	
	void SetRomInfoData(std::string r);
	
private:
	/* member functions */
	char* _itoa(int value, char* result, int base)
	{
		// check that the base is valid
		if(base < 2 || base > 16 ) { *result = 0; return result; }
		
		char* out = result;
		int quotient = value;
		
		do
		{
			*out = "0123456789abcdef"[ std::abs( quotient % base ) ];
		}
		while (quotient );
		
		// Only apply negative sign for base 10 strings
		if( value < 0 && base == 10) *out++ = '-';
		
		std::reverse( result, out );
		*out = 0;
		
		return result;
		
	}
	
	/* member variables */
	
	// Configs
	DEFAULT_DB m_currentDefaultDB;
	APP_PROFILE_CONFIG m_apc_settings;
	
	// Connections
	mysqlpp::Connection m_currentDB_Conn;
	
	// Maps
	map<std::string,std::string> m_AddressIndexes;
	
	// Operation Buckets
	map<std::string,std::string> m_CondInstructions;
	map<std::string,std::string> m_DataInstructions;
	map<std::string,std::string> m_JumpInstructions;
	map<std::string,std::string> m_LoopInstructions;
	map<std::string,std::string> m_MultiplyInstructions;
	map<std::string,std::string> m_ArithmeticInstructions;
	map<std::string,std::string> m_GenericMathInstructions;
	map<std::string,std::string> m_RegisterInstructions; // status, load, store & semaphore
	map<std::string,std::string> m_ExcepInstructions;
	
	// Static Data Bucket
	map<std::string,std::string> m_StaticData;
	
	
	// Vectors
	vector<APP_ROM_INFO *> m_Roms;
	
	// Types
	KernelType m_kernel;
	ArchType m_arch;
	OSType m_clientOS;
	ExFormat m_exe_format;
	
	// Strings
	std::string kprof;
	std::string kprofScripts;
	std::string kprofLoc;
	
	std::string arch_data;
	std::string in_data;
	std::string out_data;
	std::string dld_data;
	
	std::string res_in;
	std::string res_out;
	
	std::string emuType;
	std::string emu_path;
	std::string simuType;
	std::string simu_path;
	
	// Flags
	bool m_debug;
	bool m_daemonValue;
	bool m_ipa_Unzipped;
	bool m_app_Unzipped;
	bool m_using_emulator;
	bool m_using_simulator;
	bool m_res_files_exist;	
};

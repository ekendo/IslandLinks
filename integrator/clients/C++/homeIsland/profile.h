#include "dll.h"
#include <iostream>

USING_NAMESPACE(CryptoPP)
USING_NAMESPACE(std)

#include <fstream>
#include <vector>
#include <ctime>

// include headers that implement a archive in simple text format
#include <boost/archive/text_oarchive.hpp>
#include <boost/archive/text_iarchive.hpp>

#include "hex.h"
#include "base64.h"
#include "cryptlib.h"
#include "oids.h"
#include "eccrypto.h"


/////////////////////////////////////////////////////////////
// profile
//
// illustrates serialization for a simple type
//
class profile
{
private:
    friend class boost::serialization::access;
    // When the class Archive corresponds to an output archive, the
    // & operator is defined similar to <<.  Likewise, when the class Archive
    // is a type of input archive the & operator is defined similar to >>.
    template<class Archive>
    void serialize(Archive & ar, const unsigned int version)
    {
        ar & privateKey;
        ar & publicKey;
        ar & userName;
    }
	
	std::string privateKey;
	std::string publicKey;
	std::string userName;
public:
	DL_PrivateKey_EC< ECP > priKey;
        DL_PublicKey_EC< ECP > pubKey;
	profile(){};
	profile(std::string user) :userName(user)
	{}

	void generatePublicKey();
	void generatePublicKey(DL_PrivateKey_EC< ECP > privkey);
	void generatePrivateKey(); 
	void generatePrivateKey(int seed);
	const char * getPubKeyString();
	const char * getPriKeyString();

};

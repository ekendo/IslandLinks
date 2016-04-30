
#ifndef CRYPTOPP_DLL_ONLY
#define CRYPTOPP_DEFAULT_NO_DLL
#endif

#include <string>
#include "profile.h"


void profile::generatePublicKey()
{
	// Suppose we want to store the public key separately,
	// possibly because we will be sending the public key to a third party.
	this->priKey.MakePublicKey( pubKey );
	
}


void profile::generatePrivateKey(int seed)
{
	// ECPrivateKey is used directly only because the private key
	// won't actually be used to perform any cryptographic operation.

	printf("\tin private Key, before initialization and declaration\n");
	CryptoPP::Integer* s = new CryptoPP::Integer((float)seed);
	printf("\tin private Key, after declaration\n");

	//this->priKey.Initialize(CryptoPP::ASN1::secp256k1(),*s);
	//this->priKey.Initialize(rng,CryptoPP::ASN1::sect256k1());
	//this->priKey.Initialize(rng,CryptoPP::ASN1::sect233k1());

	this->priKey.Initialize(CryptoPP::ASN1::secp256k1(),*s);
	printf("\tin private Key, after initialization and declaration\n");
	
}

void profile::generatePublicKey(CryptoPP::DL_PrivateKey_EC< CryptoPP::ECP > privkey)
{
	// Suppose we want to store the public key separately,
	// possibly because we will be sending the public key to a third party.

	CryptoPP::DL_PublicKey_EC< CryptoPP::ECP > pubkey; 
	privkey.MakePublicKey( pubkey );
	pubKey = pubkey;
}


void profile::generatePrivateKey()
{
	// ECPrivateKey is used directly only because the private key
	// won't actually be used to perform any cryptographic operation.
	
	CryptoPP::AutoSeededRandomPool rng;
	CryptoPP::DL_PrivateKey_EC< CryptoPP::ECP > privkey;
	privkey.Initialize(rng, CryptoPP::ASN1::sect233k1()); 
	priKey = privkey;
}

const char * profile::getPubKeyString()
{
	return this->publicKey.c_str();
}

const char * profile::getPriKeyString()
{
	return this->privateKey.c_str();
}

#ifdef CRYPTOPP_IMPORTS
/* new placement
static PNew s_pNew = NULL;
static PDelete s_pDelete = NULL;

extern "C" __declspec(dllexport) void __cdecl SetNewAndDeleteFromCryptoPP(PNew pNew, PDelete pDelete, PSetNewHandler pSetNewHandler)
{
	s_pNew = pNew;
	s_pDelete = pDelete;
}

void * __cdecl operator new (size_t size)
{
	return s_pNew(size);
}

void __cdecl operator delete (void * p)
{
	s_pDelete(p);
}
*/
#endif

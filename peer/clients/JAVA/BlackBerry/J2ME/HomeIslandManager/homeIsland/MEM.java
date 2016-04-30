/*
 * islandProcessorEngineMEM.java
 *
 * © EKenDo, LLC, 2003-2009
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.engine.processor;


import java.util.*;
import java.lang.*;

/**
 * This class handles the management of memory space and addressing for
 * the virtual call stack involving function calls, relevant jumps and
 * data addressing. Also Handles Core Registers for a given arch. Bank, 
 * Flash, IO, Ram, parse_mem.
 */
public class MEM 
{
    public static Hashtable GlobalMemory; //state
    public static Hashtable GlobalMemoryMap;
   
    public static int ADDR_NOHIT = 99999;
    public static int u8 = 99999;
    
    public Hashtable MemoryBank;
    public Hashtable MemoryConfig;
    
    public BankOps bo;
    public Flash f;
    public Io io;
    public Parser p;
    public Ram ram;
   
    public class BankOps
    {
        public BankOps()
        {
            
        }
        
        public Hashtable BankPtr()
        {
            return new Hashtable();
        }
        
        public Hashtable InstructionBankPtr()
        {
            return new Hashtable();
        }
        
        public int BusRead()
        {
            return 0;
        }
        
        
        public int BusWrite()
        {
            
            return 0;
        }
        
        public Hashtable GetGlobalMemoryMap()
        {
            return new Hashtable();
        }
        
    }
    
    public class Flash
    {
        //public Hashtable DeviceDescription;
        
        public Flash()
        {
            
        }
        
        public int ReadByte(String Address)
        {
            return 0;
        }
        
        public int ReadHalfWord(String Address)
        {
            return 0;
        }
        
        public int ReadWord(String Address)
        {
            return 0;
        }
        
        public int WriteByte(Character Data, String Address)
        {
            return 0;
        }
        
        public int WriteHalfWord(Short Data, String Address)
        {
            return 0;
        }
        
        public int WriteWord(Integer Data, String Address)
        {
            return 0;
        }
        
        public int Write(int size, String Address, Object value)
        {
            return 0;
        }
        
        public int Read(int size, String Address, Object value)
        {
            return 0;
        }
    }
    
    public class Io
    {
        public Io()
        {
         
        }
     
        public void Init()
        {
            
        }
        
        public void IoReset()
        {
            
        }
        
        public void IoDoCycle()
        {
            
            
        }
        
        public void IoReadByte()
        {
            
        }
        
        public void IoReadHalfWord()
        {
            
            
        }
        
        public void IoReadWord()
        {
            
        }
        
        public void IoWriteByte()
        {
            
        }
        
        public void IoWriteHalfWord()
        {
            
            
        }
        
        public void IoWriteWord()
        {
            
        }
        
        public boolean IoRead()
        {
            
            return false;
        }        
        
        public boolean IoWrite()
        {
            return false;
        }    
    }
    
    public class Parser
    {
        public Parser()
        {
            
        }
        
        public boolean ParseMemory()
        {
            
            return false;
        }
    }
    
    public class Ram
    {
        public Ram()
        {
          
        }   
        
        public void RamReset()
        {
            
        }
        
        public void RamDoCycle()
        {
            
            
        }
        
        public void RamReadByte()
        {
            
        }
        
        public void RamReadHalfWord()
        {
            
            
        }
        
        public void RamReadWord()
        {
            
        }
        
        public void RamWriteByte()
        {
            
        }
        
        public void RamWriteHalfWord()
        {
            
            
        }
        
        public void RamWriteWord()
        {
            
        }
        
        public boolean RamRead()
        {
            
            return false;
        }        
        
        public boolean RamWrite()
        {
            return false;
        }           
    }
    
    public MEM() 
    {    
        // initialize subclasses
        
        
        
    }
    
    public void Init()
    {
        
    }
} 

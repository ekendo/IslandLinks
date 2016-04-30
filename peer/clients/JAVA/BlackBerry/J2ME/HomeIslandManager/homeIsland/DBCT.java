/*
 * islandProcessorEngineDBCT.java
 *
 * © EKenDo,LLC, 2003-2009
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.engine.processor;


import java.util.*;
import java.lang.*;

/**
 * Class handles the actual instructions that are contained
 * in the PROCESSER_* mappings. Performs operation for instructions.
 * Dynamic Binary Code Translation
 */
public class DBCT 
{
    // Speed Interval
    public static final int DBCT_TEST_SPEED_SEC = 10;
    
    // Dtaa types
    public static final int PROC_BYTE_TYPE = 0;
    public static final int PROC_HALFWORD_TYPE = 1;
    public static final int PROC_WORD_TYPE = 2;
   
    // Cache types
    public static final int NONCACHE = 0;
    public static final int DATACACHE = 1;
    public static final int INSTCACHE = 2;
   
    // class flags
    private boolean debug;
    
    // Instatiated memory data
    private Hashtable procPoints; // processor ops
    private Hashtable dbctMap; // main processor elements and events
    private Hashtable procState;
    
    // Actual Objects
    private Hashtable coProcessor;
    private Hashtable memoryManip;
    
    // Actual operation results;
    private Object dbctResult;
    private boolean isLDC;
    
    public DBCT() 
    {    
    
    }
   
    public DBCT(String instruction) 
    {    
    
    }
   
    public DBCT(Hashtable pstate)
    {
        
    }
    
    public DBCT(String instruction, Hashtable pstate)
    {
        
    }
    
    /**
     * Initialie the object container for 
     * the co processor. Should have a place 
     * for the original instruction, and a 
     * place for resulting register location.
     */
    public void InitCoProcessor()
    {
        coProcessor = new Hashtable();
        
    }
    
    // co-proc op functions
    
    public boolean Process_isCoProcLDC()
    {
        return false;
    }
    
    public boolean AddToMap_isCoProcLDC()
    {
        return false;
    }
    
    public boolean Process_isCoProcSTC()
    {
        return false;
    }
    
    public boolean AddToMap_isCoProcSTC()
    {
        return false;
    }
    
    public boolean Process_isCoProcMRC()
    {
        return false;
    }
    
     public boolean AddToMap_isCoProcMRC()
    {
        return false;
    }
    
    public boolean Process_isCoProcMCR()
    {
        return false;
    }
    
    public boolean AddToMap_isCoProcMCR()
    {
        return false;
    }
    
    public boolean Process_isCoProcCDP()
    {
        return false;
    }
    
    public boolean AddToMap_isCoProcCDP()
    {
        return false;
    }
    
    // memory op functions
    
    public boolean Process_isMemLDM()
    {
        
        return false;
    }
    
    public boolean AddToMap_isMemLDM()
    {
        return false;
        
    }
    
     public boolean Process_isMemLDM_user()
    {
        
        return false;
    }
    
    public boolean AddToMap_isMemLDM_user()
    {
        return false;
        
    }
    
    public boolean Process_isMemSTM()
    {
        return false;
        
    }
    
    public boolean AddToMap_isMemSTM()
    {
        return false;
        
    }
    
     public boolean Process_isMemSTM_user()
    {
        return false;
        
    }
    
    public boolean AddToMap_isMemSTM_user()
    {
        return false;
        
    }
    
    // data functions
    
    public Short GetProcWord()
    {
        Short word = null;
        
        return word;        
    }
    
    public void SetProcState()
    {
        
    }
   
    public Object GetOperationResults()
    {
        Object opRes = null;
        
        
        return opRes;
    }
    
    public void ExitCoProcessor()
    {
        
    }
    
    public void AttachCoProcessor()
    {
        
    }
    
    public boolean AddOpOffset()
    {
        return false;
    }
} 

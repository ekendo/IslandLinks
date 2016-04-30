/*
 * islandTravelAgent.java
 *
 * © EKenDo,LLC , 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.travel;

import java.io.*;
import java.lang.*;
import java.util.*;

import net.rim.device.api.util.*;
/**
 * This abstract class is the blueprint for classes that
 * will manage travel to and from island Link network endpoints
 * each concrete class using this should implement a different 
 * protocol. Travel Agents manager existing protocols for 
 * Applications and Users. 
 */
abstract public class islandTravelAgent 
{
    // island type
    public static final int PEER=0;
    public static final int ULTRA_PEER=1;
    public static final int VENDOR=2;
    public static final int PROVIDER=3;
    public static final int INTEGRATOR=4;  
   
    // travel type
    public static final int CONTACT = 0;
    public static final int SEND_COMAND = 2;
    public static final int RECIEVE_COMMAND = 3;
    public static final int GET = 4;
    public static final int POST = 5;
    public static final int SUBSCRIBE = 10;
    
    // travel status
    public static final int DONE  = 1;
    public static final int SUBSCRIBED = 12;
    public static final int DOWNLOADED = 6;
    public static final int POSTED = 13;
    public static final int WAIT_FOR_INPUT = 7;
    public static final int WAIT_FOR_OUTPUT = 8;
    public static final int WAIT_FOR_DOWNLOAD = 9;
    public static final int WAIT_FOR_SUBSCRIPTION = 11;
    
    // agent type
    public static final int HTTP = 0;
    public static final int YATTA = 1;
    public static final int JXTA = 2;
    public static final int GNUTELLA = 3;
    public static final int HADOOP = 4;
    public static final int PGRID = 5;
    
    /**
     * Constructor
     */
    public islandTravelAgent() 
    {    
    
    }
    
    public islandTravelAgent(int destination)
    {
        
    }
    
    public islandTravelAgent(int destination, boolean viaConnection)
    {
        
    }
    
    public abstract boolean PlanTravel();
    
    public abstract boolean BeginTravel();
    
    public abstract boolean EndTravel() throws Exception;
    
    public abstract boolean CancelTravelPlans();
    
    public abstract boolean GoingToPeer();
    
    public abstract boolean GoingToUltraPeer();
    
    public abstract boolean GoingToVendor();
    
    public abstract boolean GoingToProvider();

    public abstract boolean GoingToIntegrator();
    
    public abstract boolean NewTrip(int cmd);
    
    public abstract boolean NewTrip(int cmd, String userInput);
    
    public abstract boolean NewTrip(int cmd, String userInput, int cmdType);
    
    public abstract boolean CompleteTrip() throws Exception;

    public abstract boolean CompleteTrip(OutputStreamWriter o);
    
    public abstract boolean CompleteTrip(OutputStreamWriter o, InputStreamReader i);
    
    public abstract StringBuffer WhatHappened();
    
    public abstract DataBuffer WhatYouGot();

    public abstract String WhatYouLearned();
    
    public abstract void TravelIslandLinks(int islandlinks);
   
    public abstract void TravelViaConnection(boolean y);
    
    public abstract void SetDebug(boolean d);
      
} 

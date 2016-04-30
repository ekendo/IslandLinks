/*
 * islandTravelAgentGNUTELLA.java
 *
 * © EKenDo,LLC, 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.travel;

import java.lang.*;
import java.io.*;

import net.rim.device.api.util.*;

/**
 * 
 */
public class islandTravelAgentGNUTELLA extends islandTravelAgent
{
    public islandTravelAgentGNUTELLA() 
    {    
    
    
    }
    
    public boolean PlanTravel()
    {
        boolean travelPlanned = false;
        
        return travelPlanned;
   }
    
    public boolean BeginTravel()
    {
        boolean travelBegan = false;
    
        return travelBegan;
    }
    
    public boolean EndTravel()
    {
        boolean travelEnded = false;
        
        return travelEnded;
    }
    
    public boolean CancelTravelPlans()
    {
        boolean travelCancelled = false;
    
        return travelCancelled;
    }
    
    public boolean GoingToPeer()
    {
        boolean travellingToPeer = false;
        
        return travellingToPeer;
        
    }
    
    public boolean GoingToUltraPeer()
    {
        boolean travellingToUltraPeer = false;
    
        return travellingToUltraPeer;
    }
    
    public boolean GoingToVendor()
    {
        boolean travellingToVendor = false;
        
        return travellingToVendor;
    }
    
    public boolean GoingToProvider()
    {
        boolean travellingToProvider = false;
        
        return travellingToProvider;
    }

    public boolean GoingToIntegrator()
    {
        boolean travellingToIntegrator = false;
        
        return travellingToIntegrator;
    }
    
    public boolean NewTrip(int cmd)
    {
        boolean newTripStarted = false;
        
        return newTripStarted;
    }
    
     public boolean NewTrip(int cmd, String args)
    {
        boolean newTripStarted = false;
        
        return newTripStarted;
    }
    
    public boolean NewTrip(int cmd, String args, int cmdType)
    {
        boolean newTripStarted = false;
        
        return newTripStarted;
    }
    
    public boolean CompleteTrip()
    {
        boolean tripCompleted = false;
        
        return tripCompleted;
    }
    
    public boolean CompleteTrip(OutputStreamWriter o)
    {
        boolean tripCompleted = false;
        
        return tripCompleted;
    }

    public boolean CompleteTrip(OutputStreamWriter o, InputStreamReader i)
    {
        boolean tripCompleted = false;
        
        return tripCompleted;
    }
    
    public StringBuffer WhatHappened()
    {
        StringBuffer thisHappened = new StringBuffer();
        
        
        return thisHappened;
    }

    public DataBuffer WhatYouGot()
    {
        DataBuffer gotThis = new DataBuffer();
        
        
        return gotThis;
    }
    
    public String WhatYouLearned()
    {
        String s = "";
        
        return s;
    }
    
    public void TravelIslandLinks(int islandlinks)
    {
        
    }
    
    public void TravelViaConnection(boolean y)
    {
        
    }
    
    public void SetDebug(boolean d)
    {
        
    }
} 

/*
 * islandTravelAgentYATTA.java
 *
 * © EKenDo,LLC , 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.travel;

import java.io.*;
import java.util.*;
import java.lang.*;

import net.rim.device.api.util.*;
/**
 * 
 */
public class islandTravelAgentYATTA extends islandTravelAgent
{
    // via TCP 
    private boolean reachVerifiedLocation;
    
    // via UDP 
    private boolean reachAvailableLocation;
    
    // various flags
    private boolean debug;
    private boolean verbose;
    
    // to what Destination
    private int destination;
    private int location;
    
    // Set timeout length
    private int timeToLive;
    
    // yatta headers
    private Hashtable headers;
    
    // current travel type
    private int travelType;
    private int commandType;
    
    // current islandLinks location details
    private String category;
    private String serviceAddress;
    private String serviceName;
    private String userName;
    
    // Command Type
    private static final int ACTION = 0;
    private static final int QUERY = 1;
    
    // current baggage and movement
    private String data;
    private InputStreamReader iStream;
    private OutputStreamWriter oStream;
    private StringBuffer tripHappenings;
    private DataBuffer tripBaggage;
    
    public islandTravelAgentYATTA() 
    {    
        this.reachAvailableLocation = false;
        this.reachVerifiedLocation = false;
        this.headers = new Hashtable();
    
        this.category = "";
        this.data = "";
        this.serviceAddress = "";
        this.serviceName = "";
        this.userName = "";
        
        this.timeToLive = 30;
        
        this.travelType = islandTravelAgent.CONTACT;
        this.destination = islandTravelAgent.PROVIDER;
        this.location = islandTravelAgent.PEER;
        
        this.debug = false;
        this.verbose = false;
        
        this.tripHappenings = new StringBuffer();
        this.tripBaggage = new DataBuffer();
    }
    
    /**
     * Format the starting contact message
     * by default.
     * @return boolean
     */
    public boolean PlanTravel()
    {
        boolean travelPlanned = false;
        travelType = islandTravelAgent.GET;
       
        try
        {
            this.headers.put("peerContact","");
            this.headers.put("peerContentLength","");
            
            travelPlanned = true;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems setting yatta headers:"+ex.toString());
            }
        }
    
        return travelPlanned;
   }

    /**
     * Format the message type.
     * If command send query by default.
     * @return boolean
     */
    public boolean PlanTravel(int islandTravelType)
    {
        boolean travelPlanned = false;
        travelType = islandTravelType;
       
        try
        {
            switch(islandTravelType)
            {
                case islandTravelAgent.CONTACT:
                    this.headers.put("peerContact","");
                    break;
                case islandTravelAgent.DONE:
                    this.headers.put("peerDone","");
                    break;
            }
        
            this.headers.put("peerContactLength","");
            travelPlanned = true;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[EEROR] problems setting yatta headers:"+ex.toString());
            }
        }
        return travelPlanned;
   }


    /**
     * Format the message type.
     * If command user can choose which
     * command type to send Query or Action.
     * @return boolean
     */
    public boolean PlanTravel(int islandTravelType, int actionCommand)
    {
        boolean travelPlanned = false;
        travelType = islandTravelType;
       
        try
        {
            switch(islandTravelType)
            {
                case islandTravelAgent.CONTACT:
                    this.headers.put("peerContact","");
                    break;
                case islandTravelAgent.DONE:
                    break;
                case islandTravelAgent.SEND_COMAND:
                    if(actionCommand == ACTION)
                    {
                        this.headers.put("peerAction","");
                    }
                    else
                    {
                        this.headers.put("peerQuery","");
                    }
                    break;
            }
    
            this.headers.put("peerContentLength","");
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems setting yatta headers:"+ex.toString());
            }
        }
        return travelPlanned;
   }
    /**
     * Fill in header values if they are known at 
     * this point, format the data and send it over 
     * the wire
     * @return true if we have successfully send the 
     * data or not.false otherwise.
     */
    public boolean BeginTravel()
    {
        boolean travelBegan = false;
        
        this.data += "Yatta\r\n";
        
        for (Enumeration e = headers.keys(); e.hasMoreElements(); )
        {
            String key = (String)e.nextElement();
            String value = (String)headers.get(key);
            
            if(this.debug)
            {
                System.out.println(key + " => " + value);
            }
            
            this.data += key;
            this.data += ":";
            this.data += value;
            this.data += "\r\n";
            
        }

        this.data += "\r\n";
        
        return travelBegan;
    }

    /**
     * Fill in header values if they are known at 
     * this point, format the data and send it over 
     * the wire. If there is specifc user data used for this
     * trip then include it in the payload. 
     * @return true if we have successfully send the 
     * data or not.false otherwise.
     */
    public boolean BeginTravel(String userInput)
    {
        boolean travelBegan = false;
        
        try
        {
            this.data += "Yatta\r\n";
            
            for (Enumeration e = headers.keys(); e.hasMoreElements(); )
            {
                String key = (String)e.nextElement();
                String value = (String)headers.get(key);
                
                if(this.debug)
                {
                    System.out.println(key + " => " + value);
                }
                
                this.data += key;
                this.data += ":";
                this.data += value;
                this.data += "\r\n";
                
            }
    
            this.data += "\r\n";
            this.data += userInput;
            
            travelBegan = true;
        }
        catch(Exception err)
        {
            if(debug)
            {
                System.out.println(err.toString());
            }
        }
        
        
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

    public boolean NewTrip(int cmd, String userInput)
    {
        boolean newTripStarted = false;
        
        return newTripStarted;
    }

    public boolean NewTrip(int cmd, String userInput, int cmdType)
    {
        boolean newTripStarted = false;
        
        try
        {
            
            newTripStarted = true;
        }
        catch(Exception err)
        {
            
        }
        
        return newTripStarted;
    }
    
    /**
     * Function to send data and retrieve data. Data
     * that comes back should be put into the string
     * buffer for later retrieval. it is assumed that
     * the location and port are contained here somewhere.
     * @return a boolean flag indicating that the round 
     * trip was successful.
     */
    public boolean CompleteTrip()
    {
        boolean tripCompleted = false;
        int cnt = 0;
        char [] buffer = new char[32];
        
        try
        {
          
            // Write data to Wire
            oStream.write(data, 0, data.length());

            // Get data from wire 
            // Read character by character into the input array - we're only reading length
            // characters.
            //for (int i = 0; i < length; ++i) // Pre-increment is more efficient, as is caching the loop invariant.
            while((cnt = iStream.read(buffer,0,buffer.length))>-1)
            {
                this.tripHappenings.append(buffer,0,cnt);
            }

        }
        catch(Exception err)
        {
            if(this.debug)
            {
                System.out.println(err.toString());
            }
        }
        
        return tripCompleted;
    }

    /**
     * Function to send data and retrieve data. Data
     * that comes back should be put into the string
     * buffer for later retrieval. it is assumed that
     * the location and port are contained here somewhere.
     * @return a boolean flag indicating that the round 
     * trip was successful.
     */
    public boolean CompleteTrip(OutputStreamWriter o)
    {
        boolean tripCompleted = false;
        int cnt = 0;
        char [] buffer = new char[32];
        
        try
        {
            // Set Stream up
            this.oStream = o;
            
            // Write data to Wire
            oStream.write(data, 0, data.length());

            // Get data from wire 
            // Read character by character into the input array - we're only reading length
            // characters.
            //for (int i = 0; i < length; ++i) // Pre-increment is more efficient, as is caching the loop invariant.
            while((cnt = iStream.read(buffer,0,buffer.length))>-1)
            {
                this.tripHappenings.append(buffer,0,cnt);
            }

            
            tripCompleted = true;
        }
        catch(Exception err)
        {
            if(this.debug)
            {
                System.out.println(err.toString());
            }
        }
        
        return tripCompleted;
    }

    /**
     * Function to send data and retrieve data. Data
     * that comes back should be put into the string
     * buffer for later retrieval. it is assumed that
     * the location and port are contained here somewhere.
     * @return a boolean flag indicating that the round 
     * trip was successful.
     */
    public boolean CompleteTrip(OutputStreamWriter o, InputStreamReader i)
    {
        boolean tripCompleted = false;
        boolean afterHeader = false;
        boolean gotTripDataCnt = false;
        int cnt = 0, hIndx=0, dIndx=0, totalCnt = 0, currentCnt=0;
        String header = "";
        char[] buffer = new char[32];
        
        try
        {
            // Set Streams up
            this.oStream = o;
            this.iStream = i;
            
            // Write data to Wire
            oStream.write(data, 0, data.length());

            // Get data from wire 
            // Read character by character into the input array - we're only reading length
            // characters.
            //for (int i = 0; i < length; ++i) // Pre-increment is more efficient, as is caching the loop invariant.
            while((cnt = iStream.read(buffer,0,buffer.length))>-1)
            {
                if(this.debug)
                {
                    System.out.println("[INFO]-IslandTravel- "+new String(buffer));
                }
                
                hIndx = 0;
                currentCnt +=cnt;
                
                if(!afterHeader)
                {
                    header += new String(buffer);
                
                    if(header.indexOf("ContentLength:") > 0)
                    {
                        dIndx = header.indexOf("ContentLength:")+14;
                        String dataLen = header.substring(dIndx,header.indexOf("\r\n",dIndx));
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO]"+dataLen.trim());
                        }
                        
                        totalCnt = (int)Integer.parseInt(dataLen.trim());
                        
                        gotTripDataCnt = true;
                    }
                    
                    if(header.indexOf("\r\n\r\n") > 0)
                    {
                        hIndx = header.indexOf("\r\n\r\n");
                        afterHeader = true;
                    }
                }
                
                
                if(afterHeader)
                {
                    
                    this.tripHappenings.append(buffer,hIndx,cnt-hIndx);
                    
                    if(currentCnt >= totalCnt)
                    {
                        break;
                    }
                }
            }

            
            tripCompleted = true;
        }
        catch(Exception err)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] IslandTravel problems completing trip:"+err.toString());
            }
        }
        
        return tripCompleted;
    }
    
    public StringBuffer WhatHappened()
    {
        return tripHappenings;
    }
    
    public DataBuffer WhatYouGot()
    {
        return tripBaggage;
    }
    
    public String WhatYouLearned()
    {
        String s = "";
        
        return s;
    }

    public void TravelIslandLinks(int islandHome)
    {
        location = islandHome;
    }
    
    public void TravelViaConnection(boolean y)
    {
        if(y)
        {
            this.reachVerifiedLocation = true;
        }
        else
        {
            this.reachAvailableLocation = true;
        }
       
    }
    
    public void SetDebug(boolean d)
    {
        this.debug = d;
    }
    
    public void SetInputStream(InputStreamReader i)
    {
        this.iStream = i;
    }
    
    public void SetOutputStream(OutputStreamWriter o)
    {
        this.oStream = o;
    }
    
    public void SetCommandType(int cType)
    {
        this.travelType = islandTravelAgent.SEND_COMAND;
        this.commandType = cType;
    }
} 

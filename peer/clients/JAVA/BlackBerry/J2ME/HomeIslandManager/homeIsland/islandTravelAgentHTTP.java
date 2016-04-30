/*
 * islandTravelAgentHTTP.java
 *
 * © EKenDo,LLC , 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.travel;

import java.io.*;
import java.util.*;
import java.lang.*;

import javax.microedition.io.*;

import net.rim.device.api.system.Bitmap;

import net.rim.device.api.util.*;

/**
 * 
 */
public class islandTravelAgentHTTP extends islandTravelAgent
{
    // various flags
    private boolean debug;
    private boolean verbose;
    private boolean tripStarted;
    private boolean tripEnded;
    private boolean tripCancelled;
    
    // to what Destination
    private int destination;
    private int location;
    
    // yatta headers
    private Hashtable headers;
    
    // current travel type
    private int travelType;
    private int commandType;
    private String contentType;
    private String cookieData;
    
    // url endpoint details
    private String webSiteAddress;
    private String webSiteName;
    private String userName;
    private String passWord;
    
    // current baggage and movement
    private Byte[] bData;
    private String data;
    private String userPostMessage;
    private InputStream iStream;
    private OutputStream oStream;
    private InputStreamReader iStreamR;
    private OutputStreamWriter oStreamW;
    private HttpConnection httpConn;
    private StringBuffer tripHappenings;
    private DataBuffer tripBaggage;
    
    public islandTravelAgentHTTP() 
    {    
        this.headers = new Hashtable();
    
        this.data = "";
        this.webSiteAddress = "";
        this.webSiteName = "";
        this.userName = "";
        this.passWord = "";
        this.userPostMessage = "";
        
        this.travelType = islandTravelAgent.GET;
        this.destination = islandTravelAgent.PEER;
        this.location = islandTravelAgent.PEER;
        
        this.debug = false;
        this.verbose = false;
        this.tripCancelled = false;
        this.tripStarted = false;
        this.tripEnded = false;
       
        this.tripHappenings = new StringBuffer();
        this.tripBaggage = new DataBuffer();
        
    }
    
    public islandTravelAgentHTTP(HttpConnection c) 
    {
        this.httpConn = c;
    
        this.headers = new Hashtable();
    
        this.data = "";
        this.webSiteAddress = "";
        this.webSiteName = "";
        this.userName = "";
        this.passWord = "";
        this.userPostMessage = "";
        
        this.travelType = islandTravelAgent.GET;
        this.destination = islandTravelAgent.PEER;
        this.location = islandTravelAgent.PEER;
        
        this.debug = false;
        this.verbose = false;
        this.tripCancelled = false;
        this.tripStarted = false;
        this.tripEnded = false;
        
        this.tripHappenings = new StringBuffer();
        this.tripBaggage = new DataBuffer();
        
    }
    
    public void ResetTrip()
    {
        // flags
        this.tripCancelled = false;
        this.tripStarted = false;
        this.tripEnded = false;
        
        this.data = "";
        this.headers.clear();
        this.tripHappenings.delete(0,this.tripHappenings.length());
        this.tripBaggage.zero();
        
    }
    
    public boolean PlanTravel()
    {
        boolean travelPlanned = false;
        travelType = islandTravelAgent.GET;
       
        try
        {
            this.headers.put("peerGet","");
            this.headers.put("peerResource","");
            
            travelPlanned = true;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems setting http headers:"+ex.toString());
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
                case islandTravelAgent.GET:
                    this.headers.put("peerGet","");
                    break;
                case islandTravelAgent.POST:
                    this.headers.put("peerPost","");
                    break;
            }
        
            this.headers.put("peerResource","");
            travelPlanned = true;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[EEROR] problems setting http headers:"+ex.toString());
            }
        }
        return travelPlanned;
   }

    public boolean PlanTravel(int islandTravelType, String what)
    {
        boolean travelPlanned = false;
        travelType = islandTravelType;
       
        try
        {
            switch(islandTravelType)
            {
                case islandTravelAgent.GET:
                    this.headers.put("peerGet",what);
                    break;
                case islandTravelAgent.POST:
                    this.headers.put("peerPost",what);
                    break;
            }
        
            this.headers.put("peerResource","");
            travelPlanned = true;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[EEROR] problems setting http headers:"+ex.toString());
            }
        }
        return travelPlanned;
   }

    public boolean PlanTravel(int islandTravelType, String what, int resourceType)
    {
        boolean travelPlanned = false;
        travelType = islandTravelType;
       
        try
        {
            switch(islandTravelType)
            {
                case islandTravelAgent.GET:
                    this.headers.put("peerGet",what);
                    break;
                case islandTravelAgent.POST:
                    this.headers.put("peerPost",what);
                    break;
            }
        
            this.headers.put("peerResource",new Integer(resourceType));
            travelPlanned = true;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[EEROR] problems setting http headers:"+ex.toString());
            }
        }
        return travelPlanned;
   }
   
   public boolean PlanTravel(int islandTravelType, String what, int resourceType, String userCommand)
    {
        boolean travelPlanned = false;
        travelType = islandTravelType;
       
        try
        {
            switch(islandTravelType)
            {
                case islandTravelAgent.GET:
                    this.headers.put("peerGet",what);
                    break;
                case islandTravelAgent.POST:
                    this.headers.put("peerPost",what);
                    break;
            }
        
            this.headers.put("peerResource",new Integer(resourceType));
            this.userPostMessage = userCommand;
          
            travelPlanned = true;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[EEROR] problems setting http headers:"+ex.toString());
            }
        }
        return travelPlanned;
   }
    
    public boolean BeginTravel()
    {
        boolean travelBegan = false;
        String convertedBytes = null;
        int rc = 0;
        
        try 
        {
            if(this.travelType == islandTravelAgent.POST)
            {
                if(this.debug)
                {
                    System.out.println("[INFO] POST-with data:"+this.userPostMessage);
                }
                
                 // Set the request method and headers
                httpConn.setRequestMethod(HttpConnection.POST);
                //c.setRequestProperty("If-Modified-Since","29 Oct 1999 19:43:31 GMT");
                //httpConn.setRequestProperty("User-Agent","Profile/MIDP-2.0 Configuration/CLDC-1.0");
                httpConn.setRequestProperty("User-Agent","Mozilla/5.0 (iPhone Simulator; U; iPhone OS 2_0 like Mac OS X; en-us) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5A345 Safari/525.20");
                httpConn.setRequestProperty("Accept-Language", "en-us");
                httpConn.setRequestProperty("Cache-Control","max-age=0");

                // Getting the output stream may flush the headers
                oStream = httpConn.openOutputStream();
                oStream.write(this.userPostMessage.getBytes());
                oStream.flush();           // Optional, getResponseCode will flush
            }

             // Getting the response code will open the connection,
             // send the request, and read the HTTP response headers.
             // The headers are stored until requested.
             rc = httpConn.getResponseCode();
             
             if (rc != HttpConnection.HTTP_OK) 
             {
                 if(this.debug||this.verbose)
                 {
                    System.out.println("[INFO] HTTP response code: " + rc);
                }
             }
             else
             {
                if(this.debug)
                {
                    System.out.println("[INFO] HTTP response code:" + rc);
                }
             }
             
             travelBegan = true;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting inital http conection response:"+ex.toString());
            }
        }
        
        this.tripStarted= travelBegan;
        
        return travelBegan;
    }
    
    public boolean BeginTravel(HttpConnection c) throws IOException
    {
        boolean travelBegan = false;
        String convertedBytes = null;
        int rc = 0;
        
        try 
        {
            if(this.travelType == islandTravelAgent.POST)
            {
                if(this.debug)
                {
                    System.out.println("[INFO] POST-with data:"+this.userPostMessage);
                }
                
                 // Set the request method and headers
                c.setRequestMethod(HttpConnection.POST);
                //c.setRequestProperty("If-Modified-Since","29 Oct 1999 19:43:31 GMT");
                //c.setRequestProperty("User-Agent","Profile/MIDP-2.0 Configuration/CLDC-1.0");
                c.setRequestProperty("User-Agent","Mozilla/5.0 (iPhone Simulator; U; iPhone OS 2_0 like Mac OS X; en-us) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5A345 Safari/525.20");
                c.setRequestProperty("Accept-Language", "en-us");
                c.setRequestProperty("Accept","text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8");

                //c.setRequestProperty("Cache-Control","max-age=0");

                if(this.debug)
                {
                    if(c.getRequestProperty("Cookie")!=null)
                    {
                        System.out.println(c.getRequestProperty("Cookie"));
                    }
                    
                    if(c.getRequestProperty("Content-Type")!=null)
                    {
                        System.out.println(c.getRequestProperty("Content-Type"));
                    }
                    
                    if(c.getRequestProperty("Referer")!=null)
                    {
                        System.out.println(c.getRequestProperty("Referer"));
                    }
                    
                    if(c.getRequestProperty("Connection")!=null)
                    {
                        System.out.println(c.getRequestProperty("Connection"));
                    }
                    
                    if(c.getRequestProperty("Content-Length")!=null)
                    {
                        System.out.println(c.getRequestProperty("Content-Length"));
                    }
                }

                // Getting the output stream may flush the headers
                oStream = c.openOutputStream();
                oStream.write(this.userPostMessage.getBytes());
                oStream.flush();           // Optional, getResponseCode will flush
            }
            
             httpConn = c;
            
             // Getting the response code will open the connection,
             // send the request, and read the HTTP response headers.
             // The headers are stored until requested.
             rc = httpConn.getResponseCode();
              
             if (rc != HttpConnection.HTTP_OK) 
             {
                 if((rc == HttpConnection.HTTP_MOVED_PERM)||(rc == HttpConnection.HTTP_MOVED_TEMP))
                 {
                 
                 
                 }
                 else
                 {
                     throw new IOException("[INFO] HTTP response code: " + rc);
                 }
             }
             else
             {
                if(this.debug)
                {
                    System.out.println("[INFO] HTTP response code: " + rc);
                }
             }
             
             
             travelBegan  = true;
        }
        catch(Exception ex)
        {
            
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting initial HTTP response code:"+ex.toString());
                ex.printStackTrace();
            }
        }
        
        this.tripStarted= travelBegan;
        
        return travelBegan;
    }
    
    /**
     * Function makes sure everything is actually closed
     * for real.
     * @return <description>
     */
    public boolean EndTravel() throws Exception
    {
        boolean travelEnded = false;
        
        try
        {
            if (oStream != null)
            {
                oStream.close();
            }
           
            if (iStream != null)
            {
                 iStream.close();
            }
             
            if (httpConn != null)
            {
                httpConn.close();
            }
            
            this.tripEnded = travelEnded;
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems Ending trip by closing open connections:"+ex.toString());
            }
        }
        
        
        return travelEnded;
    }
    
    public boolean CancelTravelPlans()
    {
        boolean travelCancelled = false;
    
        return travelCancelled;
    }
    
    /**
     * Also send public key and user info 
     * just in case the other island needs to know 
     * who we are or what we are about for registration
     * etc.
     * @return boolean flag indicating that the necessary
     * steps were taken.
     */
    public boolean GoingToPeer()
    {
        boolean travellingToPeer = false;
        
        return travellingToPeer;
        
    }
    
    /**
     * Send userName , Password and public key info
     * so you will be able to loginto whatever backend
     * system exists here.
     * @return <description>
     */
    public boolean GoingToUltraPeer()
    {
        boolean travellingToUltraPeer = false;
    
        return travellingToUltraPeer;
    }
    
    /**
     * Send userName, Password, PublicKey and also an
     * ID or guid for whatever our subscription is at the 
     * moment so the payment gateway can stay informed.
     * @return <description>
     */
    public boolean GoingToVendor()
    {
        boolean travellingToVendor = false;
        
        return travellingToVendor;
    }
    
    /**
     * Set exact type of action you want to carry out
     * Just Service listing info? or also Data and a Service
     * @return <description>
     */
    public boolean GoingToProvider()
    {
        boolean travellingToProvider = false;
        
        return travellingToProvider;
    }
    
    /**
     * Explain what it is you want and for how long.
     * @return <description>
     */
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
    
    public boolean CompleteTrip() throws Exception
    {
        boolean tripCompleted = false;
        String convertedBytes = null;
        
        try
        {
            if(iStream == null)
            {
                iStream = httpConn.openInputStream();
            }
            else
            {
                // close existing stream and re-open
            
            }
            
             // Get the ContentType
             String type = httpConn.getType();
             this.contentType = type;
             if(this.debug)
             {
                System.out.println("[INFO] content Type:"+contentType);
             }

            String response = httpConn.getResponseMessage();
            String key = "";
            
            if(this.debug)
            {
                System.out.println("[INFO] response message:"+response);
            }
            
            for(int a=0; (key = httpConn.getHeaderFieldKey(a)) != null; a++)
            {
                if(this.debug)
                {
                    System.out.println("header key:"+key+",value:"+httpConn.getHeaderField(key));
                }
                if (key.equalsIgnoreCase("set-cookie"))
                {
                    this.cookieData = httpConn.getHeaderField(key);
                    // perform further cookie management
        
                    // extract Cookie name and its value from the cookie string
                } //if
            }
            
             // Get the length and process the data
             int len = (int)httpConn.getLength();
             
             if(this.debug)
             {
                System.out.println("[INFO] content Length:"+len);
             }
             
             if (len > 0) 
             {
                 int actual = 0;
                 int bytesread = 0 ;
                 byte[] data = new byte[len];
                 while ((bytesread != len) && (actual != -1)) 
                 {
                    actual = iStream.read(data, bytesread, len - bytesread);
                    bytesread += actual;
                 }
                 
                /* apparently this doesn;t work for images */
                convertedBytes = new String(data);
                this.tripHappenings.append(convertedBytes);
                
                if(this.contentType.indexOf("image")>=0)
                {
                    this.tripBaggage.write(data);
                        
                    if(this.debug)
                    {
                        /* learned that writeByteArray adds some stuff in the beginning
                        //Bitmap bmp = Bitmap.createBitmapFromBytes(UserDataDownload.getArray(),UserDataDownload.getArrayStart(),UserDataDownload.getLength(),1);  
                        Bitmap bmp1 = Bitmap.createBitmapFromPNG(data,0,len);  
                        //EncodedImage ei = EncodedImage.createEncodedImage(UserDataDownload.toArray(),0,UserDataDownload.toArray().length);
                    
                        System.out.println("Bmp Worked!");
                        System.out.println("native len:"+len);
                        System.out.println("toArray() len:"+this.tripBaggage.toArray().length);
                        System.out.println("getArray() len:"+this.tripBaggage.getArray().length);
                        System.out.println("getLength() len:"+this.tripBaggage.getLength());
                        System.out.println("getArrayLength() len:"+this.tripBaggage.getArrayLength());
                        System.out.println("startPosition:"+this.tripBaggage.getArrayStart());
                        int baggageLen = (int)this.tripBaggage.getArray().length;
                        int baggageStart = (int)this.tripBaggage.getArrayStart();
                        byte [] baggageData = (byte[])this.tripBaggage.getArray();
                        byte [] baggageToData = (byte[])this.tripBaggage.toArray();
                        System.out.println("getArray() len as int:"+baggageLen);
                        System.out.println("getArrayStart() as int:"+baggageStart);
                        System.out.println("getArray() as byte[] len:"+baggageData.length);
                        //this.tripBaggage.setLength(len);
                        //System.out.println("getLength() after setLength:"+this.tripBaggage.getLength());
                        
                        Bitmap bmp2 = Bitmap.createBitmapFromPNG(this.tripBaggage.toArray(),this.tripBaggage.getArrayStart(),this.tripBaggage.getLength()); 
                        
                        System.out.println("Bmp Worked Again!");
                        */
                    }
                }
             } 
             else 
             {
                 int ch,i=0;
                 Vector bytes = new Vector();
                 while ((ch = iStream.read()) != -1) 
                 {
                     bytes.addElement(new Byte((byte)ch));
                 }
                 
                 byte[] dataFromBytes = new byte[bytes.size()];
                 
                 for (Enumeration e = bytes.elements(); e.hasMoreElements(); ) 
                 {
                    Byte b = (Byte)e.nextElement();
                    dataFromBytes[i] = (byte)b.byteValue();
                    i++;
                 }
                 
                 convertedBytes = new String(data);
                 this.tripHappenings.append(convertedBytes);

             }
             
             tripCompleted = true;
         } 
         catch (ClassCastException e) 
         {
             if(this.debug||this.verbose)
             {
                System.out.println("[ERROR] Not an HTTP URL");
             }
         } 
         
        
        return tripCompleted;
    }
    
    public boolean CompleteTrip(HttpConnection c) throws IOException
    {   
        boolean tripCompleted = false;
        String convertedBytes = null;
        int rc = 0;
        
        try 
        {
            if(this.travelType == islandTravelAgent.POST)
            {
                if(this.debug)
                {
                    System.out.println("[INFO] POST");
                }
                
                 // Set the request method and headers
                c.setRequestMethod(HttpConnection.POST);
                //c.setRequestProperty("If-Modified-Since","29 Oct 1999 19:43:31 GMT");
                c.setRequestProperty("User-Agent","Profile/MIDP-2.0 Configuration/CLDC-1.0");
                c.setRequestProperty("Content-Language", "en-US");

                // Getting the output stream may flush the headers
                oStream = c.openOutputStream();
                oStream.write(this.userPostMessage.getBytes());
                oStream.flush();           // Optional, getResponseCode will flush
            }

             // Getting the response code will open the connection,
             // send the request, and read the HTTP response headers.
             // The headers are stored until requested.
             rc = c.getResponseCode();
             
             if (rc != HttpConnection.HTTP_OK) 
             {
                 throw new IOException("HTTP response code: " + rc);
             }
            else
             {
                if(this.debug)
                {
                    System.out.println("[INFO] HTTP response code:" + rc);
                }
             }
             
             iStream = c.openInputStream();

             // Get the ContentType
             String type = c.getType();
             this.contentType = type;

             // Get the length and process the data
             int len = (int)c.getLength();
             if (len > 0) 
             {
                 int actual = 0;
                 int bytesread = 0 ;
                 byte[] data = new byte[len];
                 while ((bytesread != len) && (actual != -1)) 
                 {
                    actual = iStream.read(data, bytesread, len - bytesread);
                    bytesread += actual;
                 }
                 
                
                /* apparently this doesn;t work for images */
                convertedBytes = new String(data);
                this.tripHappenings.append(convertedBytes);
                
                if(this.contentType.indexOf("image")>=0)
                {
                    //bData = data;
                    this.tripBaggage.writeByteArray(data);
                }
             } 
             else 
             {
                 int ch,i=0;
                 Vector bytes = new Vector();
                 while ((ch = iStream.read()) != -1) 
                 {
                     bytes.addElement(new Byte((byte)ch));
                 }
                 
                 byte[] dataFromBytes = new byte[bytes.size()];
                 
                 for (Enumeration e = bytes.elements(); e.hasMoreElements(); ) 
                 {
                    Byte b = (Byte) e.nextElement();
                    dataFromBytes[i] = (byte)b.byteValue();
                    i++;
                 }
                 
                 convertedBytes = new String(data);
                 this.tripHappenings.append(convertedBytes);
             }
             
             tripCompleted = true;
         } 
         catch (ClassCastException e) 
         {
             throw new IllegalArgumentException("Not an HTTP URL");
         } 
         finally 
         {
             if (iStream != null)
                 iStream.close();
             if (c != null)
                 c.close();
         }
         
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

    public boolean CompleteTrip(OutputStream o, InputStream i) throws Exception
    {
        boolean tripCompleted = false;
        String convertedBytes = null;
        Byte actualBytes = null;
        int rc = 0;
        
        try 
        {
            if(this.travelType == islandTravelAgent.POST)
            {
                if(this.debug)
                {
                    System.out.println("[INFO] POST");
                }
                 // Set the request method and headers
                httpConn.setRequestMethod(HttpConnection.POST);
                //c.setRequestProperty("If-Modified-Since","29 Oct 1999 19:43:31 GMT");
                httpConn.setRequestProperty("User-Agent","HomeIslandManager/Profile/MIDP-2.0 Configuration/CLDC-1.0");
                httpConn.setRequestProperty("Content-Language", "en-US");

                // Getting the output stream may flush the headers
                o = httpConn.openOutputStream();
                o.write(this.userPostMessage.getBytes());
                o.flush();           // Optional, getResponseCode will flush
            }

             // Getting the response code will open the connection,
             // send the request, and read the HTTP response headers.
             // The headers are stored until requested.
             rc = httpConn.getResponseCode();
             
             if (rc != HttpConnection.HTTP_OK) 
             {
                 throw new IOException("HTTP response code: " + rc);
             }
             else
             {
                if(this.debug)
                {
                    System.out.println("[INFO] HTTP response code:" + rc);
                }
             }

             i = httpConn.openInputStream();

             // Get the ContentType
             String type = httpConn.getType();
             this.contentType = type;

             // Get the length and process the data
             int len = (int)httpConn.getLength();
             if (len > 0) 
             {
                 int actual = 0;
                 int bytesread = 0 ;
                 byte[] data = new byte[len];
                 bData = new Byte[len];
                 while ((bytesread != len) && (actual != -1)) 
                 {
                    actual = i.read(data, bytesread, len - bytesread);
                    bytesread += actual;
                 }
                 
                /* apparently this doesn;t work for images */
                convertedBytes = new String(data);
                this.tripHappenings.append(convertedBytes);
                
                if(this.contentType.indexOf("image")>=0)
                {
                    //bData = data;
                    this.tripBaggage.writeByteArray(data);
                }
                 
             } 
             else 
             {
                 int ch,ib=0;
                 Vector bytes = new Vector();
                 while ((ch = i.read()) != -1) 
                 {
                     bytes.addElement(new Byte((byte)ch));
                 }
                 
                 byte[] dataFromBytes = new byte[bytes.size()];
                 
                 for (Enumeration e = bytes.elements(); e.hasMoreElements(); ) 
                 {
                    Byte b = (Byte)e.nextElement();
                    dataFromBytes[ib] = (byte)b.byteValue();
                    ib++;
                 }
                 
                 convertedBytes = new String(data);
                 this.tripHappenings.append(convertedBytes);

             }
         } 
         catch (ClassCastException e) 
         {
             throw new IllegalArgumentException("Not an HTTP URL");
         } 
         finally 
         {
             if (i != null)
                 i.close();
             if (httpConn != null)
                 httpConn.close();
         }
        
        return tripCompleted;
    }
    
    public StringBuffer WhatHappened()
    {
        
        return this.tripHappenings;
    }
    
    public DataBuffer WhatYouGot()
    {
        return this.tripBaggage;
    }
    
    public String WhatYouLearned()
    {
        return this.cookieData;
    }

    public void TravelIslandLinks(int islandHome)
    {
        location = islandHome;
    }
    
    /**
     * Connection is implied by the HTTP business.
     * This class always uses a connection.
     * @param y boolean flag for yes or no 
     */
    public void TravelViaConnection(boolean y)
    {
        //this.TravelViaConnection = y;
    }
    
    public void SetDebug(boolean d)
    {
        this.debug = d;
    }
    
    public void SetInputStream(InputStream i)
    {
        this.iStream = i;
    }
    
    public void SetOutputStream(OutputStream o)
    {
        this.oStream = o;
    }
    
    public boolean TripHasBegun()
    {
        return this.tripStarted;
    }
    
    public boolean TripHasBeenCancelled()
    {
        return this.tripCancelled;
    }
    
    public boolean TripHasEnded()
    {
        return this.tripEnded;
    }
} 

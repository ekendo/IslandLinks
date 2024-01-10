/*
 * islandPeerResource.java
 *
 * © EKenDo,LLC, 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.resources;

import java.lang.*;
import java.util.*;


/**
 * This class manages the resources that a user has. In
 * this case we will be managing user applications, services
 * and local domain stuffs such as profile.
 */
public class islandUserResource extends islandResource 
{
    // where is the profile
    public static final int SETTING_PROFILE_LOCATION = 55;
    
    // user data element types
    public static final int PROFILE_USER_NAME =0;
    public static final int PROFILE_PASS_WORD =1;
    public static final int PROFILE_ENVIRONMENT_SETTING = 2;
    public static final int PROFILE_ENCRYPTION_SETTING = 3;
    public static final int PROFILE_COMPRESSION_SETTING =4;
    public static final int PROFILE_PUBLIC_KEY = 5;
    public static final int PROFILE_PRIVATE_KEY = 6;
    public static final int PROFILE_HOME_ISLAND_INFO = 7;
    public static final int PROFILE_REMOTE_ISLAND_INFO = 54;
    
    // user environment types
    public static final int DESKTOP_FILE_SYSTEM = 8;
    public static final int DESKTOP_MEMORY =9;
    public static final int LAPTOP_FILE_SYSTEM = 10;
    public static final int LAPTOP_MEMORY =11;
    public static final int DEVICE_FILE_SYSTEM = 12;
    public static final int DEVICE_MEMORY = 13;
    public static final int PHONE_FILE_SYSTEM = 14;
    public static final int PHONE_MEMORY = 15;
    public static final int REMOTE_ISLAND_KNOWLEDGEBASE = 16;
    public static final int REMOTE_ISLAND_DATABASE = 17;
    public static final int REMOTE_ISLAND_FILE_SYSTEM = 18;
    public static final int REMOTE_ISLAND_MEMORY = 19;
    public static final int REMOTE_ISLAND_HARDWARE = 53; 
    public static final int HOME_ISLAND_FILE_SYSTEM = 20; // grouping 
    public static final int HOME_ISLAND_MEMORY =21;
    public static final int HOME_ISLAND_HARDWARE = 52;
    
    // resource types
    public static final int USER_APPLICATIONS = 22;
    public static final int USER_SUBSCRIPTIONS = 23;
    public static final int USER_SERVICES = 56;
    public static final int USER_SETTINGS = 24;
    public static final int USER_PROFILE = 25; // has home island details
    
    // phone resources
    public static final int USER_PHONE_CAPABILITIES = 26;
    public static final int USER_PHONE_IP = 27;
    public static final int USER_PHONE_GPS = 28;
    public static final int USER_PHONE_TELEPHONY = 29;
    public static final int USER_PHONE_VIDEO = 30;
    public static final int USER_PHONE_AUDIO = 31;
    public static final int USER_PHONE_SCREEN = 32;
    
    // device resources
    public static final int USER_DEVICE_CAPABILITIES = 33;
    public static final int USER_DEVICE_IP = 34;
    public static final int USER_DEVICE_GPS = 35;
    public static final int USER_DEVICE_TELEPHONY = 36;
    public static final int USER_DEVICE_VIDEO = 37;
    public static final int USER_DEVICE_AUDIO =38;
    public static final int USER_DEVICE_SCREEN =39;
    
    // desktop resources
    public static final int USER_DESKTOP_CAPABILITIES = 40;
    public static final int USER_DESKTOP_IP = 41;
    public static final int USER_DESKTOP_TELEPHONY = 42;
    public static final int USER_DESKTOP_VIDEO = 43;
    public static final int USER_DESKTOP_AUDIO =44;
    public static final int USER_DESKTOP_SCREEN = 45;
    
    // laptop resources
    public static final int USER_LAPTOP_CAPABILITIES = 46;
    public static final int USER_LAPTOP_IP = 47;
    public static final int USER_LAPTOP_TELEPHONY = 48;
    public static final int USER_LAPTOP_VIDEO = 49;
    public static final int USER_LAPTOP_AUDIO =50;
    public static final int USER_LAPTOP_SCREEN = 51;
    
    private Hashtable userServices;
    private Hashtable userSubscriptions;
    private Hashtable userApplications;
    private Hashtable profile;
    private Hashtable settings;
    private int type;
    private boolean debug;
    private String xmlProfile;
    
    public islandUserResource() 
    {    
        this.userServices = new Hashtable();
        this.userSubscriptions = new Hashtable();
        this.userApplications = new Hashtable();
        this.profile = new Hashtable();
        this.settings = new Hashtable();
    
        this.debug = true;
    }
    
    public void SetUserProfile(Hashtable ps)
    {
        this.profile = ps;
    }
    
    public void SetUserApps(Hashtable apps)
    {
        this.userApplications = apps;
    }
    
    public boolean HaveValidProfile()
    {
        boolean valid = false;
        
        if((this.profile.containsKey("PrivateKey"))&&
        (this.profile.containsKey("UltraPeers"))&&
        (this.profile.containsKey("PassWord"))&&
        (this.profile.containsKey("ResourceLocation")))
        {
            
            valid = true;
        }
        
        return valid;
    }
    
    public String GetDisplayFormat()
    {
        String format = "";
        
        if(this.profile.containsKey("DisplayMethod"))
        {
            format = (String) this.profile.get("DisplayMethod");
        }
        
        return format;
    }
    
    public Hashtable GetLoadedApplications()
    {
        return this.userApplications;
    }
    
    public String GetResourceLocationFor(String app)
    {
        String loc = "";
        
        try
        {
            // if the app has been loaded
            if(this.userApplications.containsKey(app))
            {
                Hashtable details = (Hashtable) this.userApplications.get(app);
        
                if(details.containsKey("ResourcesLocation"))
                {
                    loc = (String) details.get("ResourcesLocation");
                }
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting Resource Location for Application:"+ex.toString());
            }
        }
        return loc;
    }
} 

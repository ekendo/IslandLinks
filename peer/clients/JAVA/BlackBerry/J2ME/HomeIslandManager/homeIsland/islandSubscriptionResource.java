/*
 * islandSubscriptionResource.java
 *
 * © EKenDo,LLC, 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.resources;

import java.util.*;

/**
 * Class meant to manage serialized local data.
 * Saving byte[] as strings for images etc., mappings,
 * collections, and saving the entire list as a 
 * compressed, encrypted file. You subscribe to a service 
 * basically.
 */
public class islandSubscriptionResource extends islandResource
{
    // String types and Values
    private String _subscriptionName;
    private String _resourceType;
    private String _resourceValue;
    private String _applicationType;
    private String _applicationValue;
    private String _compressionType;
    private String _compressionValue;
    
    // Hashlists
    private Hashtable _resourceList; // resource type and uris foriegn and local
    private Hashtable _applicationList; // available app delivery type and name 
    private Hashtable _compressionist; // compression type and on/off flag
    
    // subscription delivery type
    public static final int UPDATE = 0;
    public static final int DOWNLOAD = 1;
    public static final int ONLINE = 2;
    public static final int UPLOAD = 3;
    
    // resource compression type
    public static final int GZIP = 0;
    public static final int ZLIB = 1;
    
    // data resource type
    public static final int XML = 0;
    public static final int COLLECTION = 1;
    
    // image media resource type
    public static final int JPG = 2;
    public static final int GIF = 3;
    public static final int BMP = 4;
    public static final int PNG = 5;
    
    // video media resource type
    public static final int MOV = 5;
    
    // audio media resource type
    public static final int WAV = 6;
    
    public islandSubscriptionResource() 
    {   
        _resourceList = new Hashtable();
    }
    
    public boolean addResource(String resource, String type)
    {
        boolean resourceAdded = false;
        
        return resourceAdded;
    }
    
    public boolean saveResourceData(int how)
    {
        boolean resourceDataSaved = false;
        
        return resourceDataSaved;
    }
    
    public boolean loadResourceData(String which)
    {
        boolean resourceLoaded = false;
        
        return resourceLoaded;
        
    }

} 

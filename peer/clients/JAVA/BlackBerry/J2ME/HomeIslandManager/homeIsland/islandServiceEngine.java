/*
 * islandServiceEngine.java
 *
 * © EKenDo, LLC 2004-2009
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.engine;

import  java.util.*;


/**
 * This class will performtasks specific to island service comands
 * whether the commands come from a local source, online source or
 * island source. The class knows how to run services 
 * coming from a translation of a variety of archetectures.
 */
public class islandServiceEngine 
{
    // class flags
    public boolean debug;
    
    // Instatiated memory data
    private Hashtable translatorPoints; // ESB, Grid, Peer Routing
    private Hashtable cloudDetails; // cloud access info
    private Hashtable remoteAccessPoints; // remote locations and types key:location
    
    // Service Type
    public static final int LOCAL = 0;
    public static final int ONLINE = 1;
    public static final int ISLAND = 2;
    
    // Engine Type
    public static final int TRANSLATION = 3;
    public static final int REMOTE_ACCESS = 4;
    public static final int CLOUD = 5;
    public static final int GRID = 6;
    public static final int ISLAND_RESOURCE = 7;
    public static final int SEMANTIC = 8;
    
    public islandServiceEngine() 
    {    
    
    }
} 

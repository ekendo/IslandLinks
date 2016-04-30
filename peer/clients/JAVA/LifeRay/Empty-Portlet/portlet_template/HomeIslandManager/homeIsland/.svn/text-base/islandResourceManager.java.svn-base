/*
 * islandResourceManager.java
 *
 * © EKenDo, 2003-2009
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.resources;

/**
 * 
 */
public class islandResourceManager 
{
    private islandResource ir = null;
    private boolean debug;
    
    public islandResourceManager() 
    {    
    
    }
    
    public islandResourceManager(boolean d) 
    {    
        this.debug = d;
    }
    
     public islandResource GetResourceFor(int domain)
    {
        switch(domain)
        {
            case islandResource.SUBSCRIPTION:
                ir = new islandSubscriptionResource();
                break;
            case islandResource.USER:
                ir = new islandUserResource();
                break;
            case islandResource.APPLICATION:
                if(this.debug)
                {
                    ir = new islandApplicationResource(this.debug);
                }
                else
                {
                    ir = new islandApplicationResource();
                }
                break;
                
        }
        
        return ir;
    }
} 

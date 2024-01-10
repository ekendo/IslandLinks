/*
 * islandTravelManager.java
 *
 * © EKenDo,LLC , 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.travel;

/**
 * Factory class used for creating different island Travel
 * Agents. Each travel agent knows the details of presenting 
 * data communucations in it's protocol.
 */
public class islandTravelManager 
{
    private islandTravelAgent ita = null;
    
    public islandTravelManager() 
    {    
        
    }
    
    public islandTravelAgent GetTravelAgent(int agent)
    {
        switch(agent)
        {
            case islandTravelAgent.GNUTELLA:
                ita = new islandTravelAgentGNUTELLA();
                break;
            case islandTravelAgent.HTTP:
                ita = new islandTravelAgentHTTP();
                break;
            case islandTravelAgent.GET:
                ita = new islandTravelAgentHTTP();
                break;
            case islandTravelAgent.POST:
                ita = new islandTravelAgentHTTP();
                break;
            case islandTravelAgent.DOWNLOADED:
                ita = new islandTravelAgentHTTP();
                break;
            case islandTravelAgent.JXTA:
                ita = new islandTravelAgentJXTA();
                break;
            case islandTravelAgent.YATTA:
                ita = new islandTravelAgentYATTA();
                break;
                
        }
        
        return ita;
    }
} 

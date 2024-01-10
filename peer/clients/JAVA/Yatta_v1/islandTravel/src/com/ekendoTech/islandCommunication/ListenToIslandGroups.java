/*
 * FindIslandGroup.java
 * This class is to find business categories or
 * people categories.
 *
 * Created on September 30, 2006, 5:03 PM
 */

package com.ekendoTech.islandCommunication;

import java.util.Enumeration;
import net.jxta.discovery.DiscoveryEvent;
import net.jxta.discovery.DiscoveryListener;
import net.jxta.discovery.DiscoveryService;
import net.jxta.document.Advertisement;
import net.jxta.peergroup.PeerGroup;
import net.jxta.protocol.DiscoveryResponseMsg;
import net.jxta.protocol.PeerGroupAdvertisement;
import com.ekendoTech.islandTravel.IslandTravelAgentJXTA;
import com.ekendoTech.islandTravel.IslandTravelAgentHTTP;
import com.ekendoTech.islandTravel.IslandTravelAgentGNUTELLA;

/**
 *
 * @author Administrator
 */
public class ListenToIslandGroups implements DiscoveryListener
{
    
    // Static variables. 
    private transient static PeerGroup netPeerGroup = null;
    private transient static PeerGroupAdvertisement groupAdvertisement = null;
    
    // Travel Agents.
    private transient IslandTravelAgentJXTA jxtaManager;
    private transient IslandTravelAgentGNUTELLA gnutellaManager;
    private transient IslandTravelAgentHTTP httpManager;
    private transient DiscoveryService discovery;
    
    // How are we going to be gettin about.
    public enum TravelAgent 
    { JXTA, GNUTELLA, HTTP}
    
    /** Creates a new instance of FindIslandGroup */
    public ListenToIslandGroups(TravelAgent agent) 
    {
        switch(agent.ordinal())
        {
        
            case 0:
                    jxtaManager = new IslandTravelAgentJXTA("DiscoveryClient");
                    jxtaManager.start("principal", "password");
                    
                    //Get the NetPeerGroup
                    netPeerGroup = jxtaManager.getNetPeerGroup();
                    
                    // get the discovery service
                    discovery = netPeerGroup.getDiscoveryService();
                    
                    // obtain our group advertisement
                    groupAdvertisement = netPeerGroup.getPeerGroupAdvertisement();
                     
                    // Break out
                    break;
            default :
                    httpManager = new IslandTravelAgentHTTP();
                    break;
                   
        }
    }
    
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) 
    {
        ListenToIslandGroups discoveryClient = new ListenToIslandGroups(ListenToIslandGroups.TravelAgent.JXTA);
        discoveryClient.start();
    }
    
     /**
     * loop forever attempting to discover advertisements every minute
     */
    public void start() {
        long waittime = 60 * 1000L;
        try {
            // Add ourselves as a DiscoveryListener for DiscoveryResponse events
            discovery.addDiscoveryListener(this);
            discovery.getRemoteAdvertisements(
                    // no specific peer (propagate)
                    null,
                    //Adv type
                    DiscoveryService.ADV,
                    //Attribute = any
                    null,
                    //Value = any
                    null,
                    // one advertisement response is all we are looking for
                    1,
                    // no query specific listener. we are using a global listener
                    null);
            while (true) {
                // wait a bit before sending a discovery message
                try {
                    System.out.println("Sleeping for :"+waittime);
                    Thread.sleep(waittime);
                } catch (Exception e) {}
                System.out.println("Sending a Discovery Message");
                // look for any peer
                discovery.getRemoteAdvertisements(
                    // no specific peer (propagate)
                    null,
                    //Adv type
                    DiscoveryService.ADV,
                    //Attribute = name
                    "Name",
                    //Value = the tutorial
                    "Discovery tutorial",
                    // one advertisement response is all we are looking for
                    1,
                    // no query specific listener. we are using a global listener
                    null);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * This method is called whenever a discovery response is received, which are
     * either in response to a query we sent, or a remote publish by another node
     *
     * @param  ev  the discovery event
     */
    public void discoveryEvent(DiscoveryEvent ev) {

        DiscoveryResponseMsg res = ev.getResponse();
        // let's get the responding peer's advertisement
        System.out.println(" [  Got a Discovery Response [" +
                res.getResponseCount() + " elements]  from peer : " + ev.getSource() + "  ]");

        Advertisement adv = null;
        Enumeration en = res.getAdvertisements();
        if (en != null) {
            while (en.hasMoreElements()) {
                adv = (Advertisement) en.nextElement();
                System.out.println(adv);
            }
        }
    }

    /**
     * Stops the platform
     */
    public void stop()
    {
        //Stop JXTA
        jxtaManager.stop();
    }
    
}

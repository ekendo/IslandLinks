/*
 * SpeakToIslandGroups.java
 *
 * Created on September 30, 2006, 8:16 PM
 */

package com.ekendoTech.islandCommunication;

import java.util.Enumeration;
import net.jxta.discovery.DiscoveryEvent;
import net.jxta.discovery.DiscoveryListener;
import net.jxta.discovery.DiscoveryService;
import net.jxta.document.Advertisement;
import net.jxta.document.AdvertisementFactory;
import net.jxta.id.IDFactory;
import net.jxta.peergroup.PeerGroup;
import net.jxta.peergroup.PeerGroupID;
import net.jxta.pipe.PipeService;
import net.jxta.protocol.DiscoveryResponseMsg;
import net.jxta.protocol.PeerGroupAdvertisement;
import net.jxta.protocol.PipeAdvertisement;
import com.ekendotech.islandtravel.IslandTravelAgent;
/**
 *
 * @author Administrator
 */
public class SpeakToIslandGroups  implements DiscoveryListener
{
    
    private transient static PeerGroup netPeerGroup = null;
    private transient static PeerGroupAdvertisement groupAdvertisement = null;
    private transient IslandTravelAgent manager;
    private transient DiscoveryService discovery;
    
    /** Creates a new instance of SpeakToIslandGroups */
    public SpeakToIslandGroups() 
    {
        manager = new IslandTravelAgent("DiscoveryServer");
        manager.start("principal", "password");
        //Get the NetPeerGroup
        netPeerGroup = manager.getNetPeerGroup();
        // get the discovery service
        discovery = netPeerGroup.getDiscoveryService();
        // obtain our group advertisement
        groupAdvertisement = netPeerGroup.getPeerGroupAdvertisement();
    }
    
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) 
    {
        SpeakToIslandGroups discoveryServer = new SpeakToIslandGroups();
        discoveryServer.start();
    }
    
    /**
     * create a new pipe adv, publish it for 2 minut network time, 
     * sleep for 3 minutes, then repeat
     * 
     */
    public void start() {
        long lifetime = 60 * 2 * 1000L;
        long expiration = 60 * 2 * 1000L;
        long waittime = 60 * 3 * 1000L;

        try {
            while (true) {
                PipeAdvertisement pipeAdv = getPipeAdvertisement();
                // publish the advertisement with a lifetime of 2 mintutes
                System.out.println("Publishing the following advertisement with lifetime :"+lifetime+" expiration :"+expiration);
                System.out.println(pipeAdv.toString());
                discovery.publish(pipeAdv, lifetime, expiration);
                discovery.remotePublish(pipeAdv, expiration);
                try {
                    System.out.println("Sleeping for :"+waittime);
                    Thread.sleep(waittime);
                } catch (Exception e) {}
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
     * Creates a pipe advertisement
     *
     * @return    a Pipe Advertisement
     */
    public static PipeAdvertisement getPipeAdvertisement() 
    {
        PipeAdvertisement advertisement = (PipeAdvertisement)
                                          AdvertisementFactory.newAdvertisement(PipeAdvertisement.getAdvertisementType());
        advertisement.setPipeID(IDFactory.newPipeID(PeerGroupID.defaultNetPeerGroupID));
        advertisement.setType(PipeService.UnicastType);
        advertisement.setName("Discovery tutorial");
        return advertisement;
    }

    /**
     * Stops the platform
     */
    public void stop() 
    {
        //Stop JXTA
        manager.stop();
    }
}

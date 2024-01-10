/*
 * IslandTravelMain.java
 *
 * This class is to manage the connection to given Groups
 * on the JXTA network.
 * TODO: implementation of "Travel" to business groups
 *  ======> running peer services or as regular peers
 *  ======> connection to business Groups/Categories
 *  ==========> save locally
 *  ==========> connect to group listing of Group IP's to save
 * Created on September 29, 2006, 3:17 PM
 */

package com.ekendoTech.islandTravel;

import net.jxta.peergroup.PeerGroup;
import net.jxta.peergroup.NetPeerGroupFactory;
import net.jxta.exception.PeerGroupException;
import net.jxta.discovery.DiscoveryEvent;
import net.jxta.discovery.DiscoveryListener;
import net.jxta.discovery.DiscoveryService;
import net.jxta.document.Advertisement;
import net.jxta.protocol.DiscoveryResponseMsg;
import net.jxta.protocol.PeerGroupAdvertisement;
import java.io.File;
import java.io.IOException;
import java.net.URI;
import net.jxta.credential.AuthenticationCredential;
import net.jxta.credential.Credential;
import net.jxta.document.Advertisement;
import net.jxta.exception.PeerGroupException;
import net.jxta.id.IDFactory;
import net.jxta.impl.membership.pse.StringAuthenticator;
import net.jxta.membership.InteractiveAuthenticator;
import net.jxta.membership.MembershipService;
import net.jxta.platform.NetworkConfigurator;
import net.jxta.peergroup.PeerGroupFactory;
import net.jxta.peergroup.PeerGroupID;
import net.jxta.rendezvous.RendezvousEvent;
import net.jxta.rendezvous.RendezvousListener;
import net.jxta.rendezvous.RendezVousService;

/**
 *
 * @author Administrator
 */
public class IslandTravelAgentJXTA implements RendezvousListener
{

    private PeerGroup netPeerGroup = null;
    private boolean started = false;
    private boolean stopped = false;
    private RendezVousService rendezvous;
    private final static String connectLock = new String("connectLock");
    private String instanceName = "NA";
    private final static File home = new File(System.getProperty("JXTA_HOME", ".cache"));


    /** Creates a new instance of IslandTravelMain */
    public IslandTravelAgentJXTA(String instanceName)
    {
        this.instanceName = instanceName;


    }

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args)
    {
        System.out.println("Starting JXTA ....");
        IslandTravelAgentJXTA manager = new IslandTravelAgentJXTA("EKenDo_Travel_Agent");
        System.out.println("Starting NetworkManager ....");
        manager.start("principal", "password");
        PeerGroup netPG = manager.getNetPeerGroup();
        manager.waitForRendezvousConncection(10000);
        System.out.println("Good Bye ....");
        manager.stop();
    }

    /**
     *  Creates and starts the JXTA NetPeerGroup using a platform configuration
     *  template. This class also registers a listener for rendezvous events
     *
     * @param  principal  principal used the generate the self signed peer root cert
     * @param  password   the root cert password
     */
    public synchronized void start(String principal, String password)
    {
        if (started)
        {
            return;
        }
        try {
            File instanceHome = new File(home, instanceName);
            NetworkConfigurator config = new NetworkConfigurator();
            config.setHome(instanceHome);
            if (!config.exists())
            {
                config.setPeerID(IDFactory.newPeerID(PeerGroupID.defaultNetPeerGroupID));
                config.setName(instanceName);
                config.setDescription("Created by Network Manager");
                config.setMode(NetworkConfigurator.EDGE_NODE);
                config.setPrincipal(principal);
                config.setPassword(password);
                try {
                    config.addRdvSeedingURI(new URI("http://rdv.jxtahosts.net/cgi-bin/rendezvous.cgi?2"));
                    config.addRelaySeedingURI(new URI("http://rdv.jxtahosts.net/cgi-bin/relays.cgi?2"));
                } catch (java.net.URISyntaxException use) {
                    use.printStackTrace();
                }
                try {
                    config.save();
                } catch (IOException io) {
                    io.printStackTrace();
                }
            }
            // create, and Start the default jxta NetPeerGroup
            netPeerGroup = PeerGroupFactory.newNetPeerGroup();
            System.out.println("Node PeerID :"+netPeerGroup.getPeerID().getUniqueValue().toString());
            rendezvous = netPeerGroup.getRendezVousService();
            rendezvous.addListener(this);
            started = true;
        } catch (PeerGroupException e) {
            // could not instantiate the group, print the stack and exit
            System.out.println("fatal error : group creation failure");
            e.printStackTrace();
            System.exit(1);
        }
    }

    /**
     *  Establishes group credential.  This is a required step when planning to
     *  to utilize TLS messegers or secure pipes
     *
     * @param  group      peer group to establish credentials in
     * @param  principal  the principal
     * @param  password   pass word
     */
    public static void login(PeerGroup group, String principal, String password)
    {
        try {
            StringAuthenticator auth = null;
            MembershipService membership = group.getMembershipService();
            Credential cred = membership.getDefaultCredential();
            if (cred == null) {
                AuthenticationCredential authCred = new AuthenticationCredential(group, "StringAuthentication", null);
                try {
                    auth = (StringAuthenticator) membership.apply(authCred);
                } catch (Exception failed) {
                    ;
                }

                if (auth != null) {
                    auth.setAuth1_KeyStorePassword(password.toCharArray());
                    auth.setAuth2Identity(group.getPeerID());
                    auth.setAuth3_IdentityPassword(principal.toCharArray());
                    if (auth.isReadyForJoin()) {
                        membership.join(auth);
                    }
                }
            }

            cred = membership.getDefaultCredential();
            if (null == cred) {
                AuthenticationCredential authCred = new AuthenticationCredential(group, "InteractiveAuthentication", null);
                InteractiveAuthenticator iAuth = (InteractiveAuthenticator) membership.apply(authCred);
                if (iAuth.interact() && iAuth.isReadyForJoin()) {
                    membership.join(iAuth);
                }
            }
        } catch (Throwable e) {
            // make sure output buffering doesn't wreck console display.
            System.err.println("Uncaught Throwable caught by 'main':");
            e.printStackTrace();
            System.exit(1);
        } finally {
            System.err.flush();
            System.out.flush();
        }
    }

    /**
     *  Stops and unrefrences the NetPeerGroup
     */
    public synchronized void stop()
    {
        if (stopped && !started) {
            return;
        }
        rendezvous.removeListener(this);
        netPeerGroup.stopApp();
        netPeerGroup.unref();
        netPeerGroup = null;
        stopped = true;
    }

    /**
     *  Gets the netPeerGroup object
     *
     * @return    The netPeerGroup value
     */
    public PeerGroup getNetPeerGroup()
    {
        return netPeerGroup;
    }

    /**
     * Blocks if not connected to a rendezvous, or
     * until a connection to rendezvous node occurs
     *
     * @param  timeout  timeout in milliseconds
     */
    public void waitForRendezvousConnection(long timeout)
    {
        if (!rendezvous.isConnectedToRendezVous() || !rendezvous.isRendezVous()) {
            System.out.println("Waiting for Rendezvous Connection");
            try {
                if (!rendezvous.isConnectedToRendezVous()) {
                    synchronized (connectLock) {
                        connectLock.wait(timeout);
                    }
                }
                System.out.println("Connected to Rendezvous");
            } catch (InterruptedException e) {}
        }
    }

    /**
     *  rendezvousEvent the rendezvous event
     *
     * @param  event  rendezvousEvent
     */
    public void rendezvousEvent(RendezvousEvent event)
    {
        if (event.getType() == event.RDVCONNECT ||
            event.getType() == event.RDVRECONNECT ||
            event.getType() == event.BECAMERDV)
        {

            switch(event.getType())
            {
                case RendezvousEvent.RDVCONNECT :
                    System.out.println("Connected to rendezvous peer :"+event.getPeerID());
                    break;
                case RendezvousEvent.RDVRECONNECT :
                    System.out.println("Reconnected to rendezvous peer :"+event.getPeerID());
                    break;
                case RendezvousEvent.BECAMERDV :
                    System.out.println("Became a Rendezvous");
                    break;
            }

            synchronized (connectLock)
            {
                connectLock.notify();
            }
        }
    }


}

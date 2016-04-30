/*
 * IslandGui.java
 *
 * © EKenDo,LLC, 2003-2009
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.gui;

import java.io.*;
import java.util.*;

import javax.microedition.io.*;

import com.ekendotech.homeIsland.engine.*;
import com.ekendotech.homeIsland.resources.*;
import com.ekendotech.homeIsland.travel.*;

import net.rim.device.api.util.*;

import net.rim.device.api.ui.component.*;
import net.rim.device.api.ui.container.*;
import net.rim.device.api.ui.*;

import net.rim.device.api.system.Bitmap;
//import net.rim.device.api.system.Characters;
//import net.rim.device.api.system.Application;
/**
 *  Class used as the main entry point into island links.
 *  This will manage the windows necessary to start and manage
 *  the framework functionality. Basic methodology here is to 
 *  'get' from the network, 'load' from a resource, 'save' to memory or disk,
 *  'create' in memory, 'show' in gui.
 */
public class IslandGui extends UiApplication 
{
    /* public variables */
    public islandTravelManager Tm;
    public String UserCommands;
    public String UserMessage;
    public String UserData;
    public String UserCookie;
    public String UserDownload;
    public DataBuffer UserDataDownload;
    
    // status & state
    public static int IslandTravelState;
    public static int IslandResourceState;
    public static int IslandSoftwareState;
   
    // display Types
    public static final int DISPLAY_IN_TEXT = 0;
    public static final int DISPLAY_IN_DIALOG_TEXT = 1;
    public static final int DISPLAY_IN_ASK_DIALOG_TEXT = 2;
    public static final int DISPLAY_IN_DIALOG_FORM_TEXT = 3;
    public static final int DISPLAY_GRAPHIC_OBJECTS = 4;
    public static final int DISPLAY_GUI_COMPONENTS = 5;
    public static final int DISPLAY_GRID_OVERLAYS = 6;
    
    /* private variables */
    private static IslandGui _homeIsland;
    
    private static String PROD_URL = "socket://65.99.212.4:32920;deviceside=true"; // default provider
    private static String TEST_URL = "socket://72.249.185.247:32920;deviceside=true"; // default provider
    private static String DEV_URL = "socket://192.168.15.29:32920;deviceside=true"; // default provider
    private static String LOCATION = "file:////HomeIslandData/"; // local disk
    private static String DOWNLOAD = "http://ekendotech.net/Downloads/UniApp/TouchCalc_Res.xml";
    private static String LOADED_APP = "TouchCalc";
    private static String LOADED_SERVICE = "UniApp";
    private static String LOADED_SUBSCRIPTION = "";
    private static String PRIVATE_KEY = "AAAABBBBCCCCDDDDEEEFFFGGGG";
    private static String PUBLIC_KEY = "!@#$%^&*(AAABBBCCCDDDEEEFFFFGGGG";
    private static String CURRENT_USER = "KenYahhTah";
    
    private final String spaceChar = " ";
    
    // in memory storage objects
    private Hashtable defaultIntegrators = null;
    private Hashtable defaultProviders = null;
    private Hashtable defaultVendors = null;
    private Hashtable defaultUltraPeers = null;
    private Hashtable defaultPeers = null;
    
    private Hashtable availableSubscriptions = null;
    private Hashtable subscriptionDetails = null;
    private Hashtable subscriptionCommands = null;
    
    private Hashtable availableServices = null;
    private Hashtable serviceDetails = null;
    private Hashtable serviceCommands = null;
    
    private Hashtable availableApplications = null;
    private Hashtable applicationDetails = null;
    private MultiMap applicationTranslationMapping = null;
    private MultiMap applicationResourceValues = null;
    private MultiMap applicationOperationMapping = null;
    private MultiMap applicationAddressValues = null;
    
    private Hashtable profileSettings = null;
    private Hashtable userDetails = null;
    
    static final boolean debug = true;
    private boolean _loadedLabels = false;
    private final boolean drawRed = false;
    

    private Font yattaFont = null;
    
    public static void main(String[] args)
    {
        /*  Starting point with */
        _homeIsland = new IslandGui();
        _homeIsland.enterEventDispatcher();
    }
    
    /**
     * Constructor
     */
    public IslandGui() 
    {    
        try
        {
            defaultIntegrators = new Hashtable();
            defaultProviders = new Hashtable();
            defaultVendors = new Hashtable();
            defaultUltraPeers = new Hashtable();
            defaultPeers = new Hashtable();
        
            availableApplications = new Hashtable();
            applicationDetails = new Hashtable();
            applicationTranslationMapping = new MultiMap();
            applicationResourceValues = new MultiMap();
            
            availableSubscriptions = new Hashtable();
            subscriptionDetails = new Hashtable();
            subscriptionCommands = new Hashtable();
            
            availableServices = new Hashtable();
            serviceDetails = new Hashtable();
            serviceCommands = new Hashtable();
        
            profileSettings = new Hashtable();
            userDetails = new Hashtable();
            
            // connection related stuffs
            UserDataDownload = new DataBuffer();
            Tm = new islandTravelManager();
            UserCommands = "";
            UserMessage = "";
            UserDownload = "";
        
            // Initialization
            newIsland();
            
            // MainScreen is the basic screen or frame class of the RIM UI.
            _mainScreen = new HomeIslandGUIScreen(Field.USE_ALL_HEIGHT|Field.USE_ALL_WIDTH);

            // Add a field to the title region of the MainScreen. We use a simple LabelField
            // here. The ELLIPSIS option truncates the label text with "..." if the text 
            // was too long for the space available.
            //_mainScreen.setTitle(new LabelField("Home Island Manager" , LabelField.ELLIPSIS | LabelField.USE_ALL_WIDTH));
           _mainScreen.setTitle(new LabelField("Uni-App" , LabelField.ELLIPSIS | LabelField.USE_ALL_WIDTH));

            // Push the MainScreen instance onto the UI stack for rendering.
            pushScreen(_mainScreen);
            
        }
        catch(Exception e)
        {
            if(debug)
            {
                System.out.println("[ERROR] problems loading the homeGui:"+e.toString());
            }
        }
    }
    
    /**
     * Initialize the variables and clear the board to start
     * a new game (re-uses the main screen).
     */
    private void newIsland() 
    {
        try
        {
            defaultIntegrators.clear();
            defaultProviders.clear();
            defaultVendors.clear();
            defaultUltraPeers.clear();
            defaultPeers.clear();
            
            availableSubscriptions.clear();
            subscriptionDetails.clear();
            subscriptionCommands.clear();
            
            availableServices.clear();
            serviceDetails.clear();
            serviceCommands.clear();
            
            availableApplications.clear();
            applicationDetails.clear();
            applicationTranslationMapping.clear();
            applicationResourceValues.clear();
            
            profileSettings.clear(); 
            userDetails.clear();
        
            IslandTravelState = islandTravelAgent.CONTACT;
       
            // DEV
            defaultProviders.put("localhost.net","192.168.15.29");
       
            // TEST
            //defaultProviders.put("roadwarrior.net","");
       
            // PROD
            //defaultProviders.put("ekendotech.net","");
       
            defaultVendors.put("UltimateProxyList", "http://ultimateproxylist.com");
            defaultVendors.put("IntelligentMapping","http://theuniapp.com");
            defaultVendors.put("EKenDo","yatta://ekendotech.net");
            defaultVendors.put("YourNerdz","yatta://ekendotech.com");
            defaultVendors.put("BizzleMe","yatta://roadwarrior.net");
            
            // Hard coded Ultra-Peers
            defaultUltraPeers.put("EKenDo-Members","yatta://ekendotech.net");
            defaultUltraPeers.put("BizzleMe-Members","yatta://bizzleme.info");
            defaultUltraPeers.put("UltimateProxyList-Members","yatta://ultimateproxylist.com");
       
            // Hard coded Peers, UltraPeers
            defaultPeers.put("ekendo","yatta://ekendotech.net");
            defaultPeers.put("egrant","yatta://ekendotech.net");

            
             // Hard Code Service Commands
            subscriptionCommands.put("GetAvailableApplications","-a");
            subscriptionCommands.put("GetApplicationDetails","-d");
            subscriptionCommands.put("GetAvailablePlatforms","-p");
            subscriptionCommands.put("GetTranslatorMapping","-t");
            subscriptionCommands.put("GetOperationMapping","-o");
            subscriptionCommands.put("GetApplicationResources","-r");
            subscriptionCommands.put("GetApplicationAddresses","-l");
        
            // Hard Coded Service is UniApp usually gotten through provider Service Listing
            subscriptionDetails.put("Description", "Semantic Mobile Application Details");
            subscriptionDetails.put("UsesYatta",new Boolean(true));
            subscriptionDetails.put("HasApplications",new Boolean(true)); 
            subscriptionDetails.put("HasKnowledgeBases",new Boolean(false)); // utilizes sematic web?
            subscriptionDetails.put("HasSubscriptions",new Boolean(false)); // Manages users for a goal?
            subscriptionDetails.put("HasResources",new Boolean(false));
            subscriptionDetails.put("RunsLocal",new Boolean(true));
            subscriptionDetails.put("RunsOnline",new Boolean(false));
            subscriptionDetails.put("RunsOnlinks",new Boolean(true));
            subscriptionDetails.put("ResourcesLocation","");
            subscriptionDetails.put("ServiceCommandsLoaded",new Boolean(true));
            subscriptionDetails.put("SubscriptionCommands",subscriptionCommands);

            // Hard coded Subscriptions
            //availableSubscriptions.put("IslandProxy","yatta://ekendotech.net");
            //availableSubscriptions.put("IslandChat","yatta://roadwarrior.net");
            availableSubscriptions.put("UniApp",subscriptionDetails);
            
            // Hard coded Services - subscriptions
            availableServices.put("IntelligentMapping",availableSubscriptions);
            
            
            applicationTranslationMapping = new MultiMap();
            applicationOperationMapping = new MultiMap();
            applicationResourceValues = new MultiMap();
            applicationAddressValues = new MultiMap();
            applicationDetails = new Hashtable();
            
            // Touch Calc Hard Coded App Mapping - showing PROCESSOR, GUI & EVENT translation
            // Settings
            //applicationOperationMapping.add(new Integer(islandProcessorEngine.PROCESSOR_ARM_ARCH),"ARCH_ARMv6:MMU"); // proc
            //applicationOperationMapping.add(new Integer(islandProcessorEngine.PROCESSOR_ARM_ARCH),"ARCH_ARMv6:DACT"); // proc
            
            applicationTranslationMapping.add(new Integer(islandSoftwareEngine.APP_GRID_EVENT), "CalcDisplay[0]_Write:Scientific[0.0]");
            applicationTranslationMapping.add(new Integer(islandSoftwareEngine.APP_GRID_EVENT), "CalcDisplay[1]_Write:BitInteger[12.0]");
            applicationTranslationMapping.add(new Integer(islandSoftwareEngine.APP_GRID_EVENT), "CalcDisplay[2]_Write:Statistics[24.0]");
            
            
            applicationAddressValues.add(new Integer(islandSoftwareEngine.KERNEL_MEMORY),"Offset_Set[23]-0x02000"); // where to begin value
            applicationAddressValues.add(new Integer(islandSoftwareEngine.KERNEL_MEMORY),"Start_Set[24]-0x02000"); // where to begin location
            
            // Function Addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.KERNEL_MEMORY),"DyldStubBindingHelper_Set[25]-0x0204C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.KERNEL_MEMORY),"ApplicationDidFinishLaunching_Set[26]-0x020BC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.KERNEL_MEMORY),"Dealloc_Set[27]-0x0246C"); // addresses
            
            // Function Addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"UpdateButtonStates[28]_Set-0x02548"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"CreateButtonSet[29]_Set-0x02670"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ShowMemory[30]_Set-0x03114"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ShowBrackets[31]_Set-0x031A0"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ShowResult[32]_Set-0x031C8"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ShowStatistics[33]_Set-0x0331C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"CalcResult[34]_Set-0x037AC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ProcessStatiscticsOp[35]_Set-0x03D8C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ProcessOp[36]_Set-0x044E4"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ButtonPressed[37]_Set-0x04E48"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ContentView[38]_Set-0x051D0"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetContentView[39]_Set-0x051DC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetWindow[40]_Set-0x05208"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetFrame[41]_Set-0x05F20"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"BinaryString[42]_Set-0x05FB4"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetMain[43]_Set-0x06020"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetMemory[44]_Set-0x061A4"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetBracketLevel[45]_Set-0x061CC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetUniCode[46]_Set-0x06294"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"LeftOperator[47]_Set-0x06338"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"RightOperator[48]_Set-0x06344"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetLeftOperator[49]_Set-0x06350"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"Priority[50]_Set-0x063B8"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"CalcAsInt[51]_Set-0x063BC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"RemoveAll[52]_Set-0x06794"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PushRoot[53]_Set-0x06940"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PushAnd[54]_Set-0x06988"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PushOr0[55]_Set-0x06A12"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PushOr[56]_Set-0x06A18"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PushNor[57]_Set-0x06A60"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PushXor[58]_Set-0x06AA8"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PushOpenBracket[59]_Set-0x06C58"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"IntWithNumber[60]_Set-0x06DEC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetLeftOperator[61]_Set-0x06E28"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetRightOperator[62]_Set-0x06E2A"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"NumberNodeCalc[63]_Set-0x06E2C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SetValue[64]_Set-0x06E3C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"NumberNodePriority[65]_Set-0x06E4C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PlusNodePriority[66]_Set-0x06F18"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"Sub6FA4[67]_Set-0x06FA4"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"MinusNodePriority[68]_Set-0x06FE8"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"MultNodeCalc[69]_Set-0x06FEC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"DivNodePriority[70]_Set-0x07190"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PowNodePriority[71]_Set-0x0720C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"RootNodePriority[72]_Set-0x07314"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"BracketNodeCalc[73]_Set-0x07318"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"BracketNodeSetRightOperator[74]_Set-0x07338"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"BracketNodePriority[75]_Set-0x07360"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ANDNodeCalc[76]_Set-0x07364"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ANDNodePriority[77]_Set-0x073E4"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ORNodeCalc[78]_Set-0x073E8"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ORNodePriority[79]_Set-0x07468"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"XORNodeCalc[80]_Set-0x0746C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"XORNodePriority[81]_Set-0x074EC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SHLNodeCalc[82]_Set-0x074F0"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SHLNodePriority[83]_Set-0x07578"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"SHRNodePriority[84]_Set-0x07604"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"NANDNodeCalc[85]_Set-0x07608"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"NANDNodePriority[86]_Set-0x07688"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"NORNodePriority[87]_Set-0x0770C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"XNORNodePriority[88]_Set-0x07790"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ROLNodePriority[89]_Set-0x07794"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"RORNodeCalc[90]_Set-0x0779C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ROLNodeCalc[91]_Set-0x07888"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PreferencesViewDealloc[92]_Set-0x079EC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PreferencesViewSetTarget[93]_Set-0x07A28"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PreferencesViewSetAction[94]_Set-0x07A34"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PreferencesViewSoundSelected[95]_Set-0x08D30"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PreferencesViewBitsSelected[96]_Set-0x08D60"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PreferencesViewPrecisionChanged[97]_Set-0x08DF0"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"PreferencesViewNumberOfSelectionsInTableView[98]_Set-0x08F28"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"Sub8F3C[99]_Set-0x08F3C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"TileForHeaderInSection[184]_Set-0x08F44"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"TileForFooterInSection[185]_Set-0x08F6C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"FpPrecision[186]_Set-0x0905C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"IntToReal[187]_Set-0x09312"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"Switch8[188]_Set-0x093F4"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"Switch16[189]_Set-0x0940C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ACosH[190]_Set-0x0957C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ASin[191]_Set-0x09588"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ASinH[192]_Set-0x09594"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ATan[193]_Set-0x095A0"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"ATanH[194]_Set-0x095AC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"Cos[195]_Set-0x095B8"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"Log10[196]_Set-0x095DC"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"StrChr[197]_Set-0x09660"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"Tan[198]_Set-0x0966C"); // addresses
            applicationAddressValues.add(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION),"TanH[199]_Set-0x09678"); // addresses
            
            // Import Lib/API Function Addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"Foundation[184]-NSAutoReleasePool[100]_Set-0x197A0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"Foundation[184]-NSBundle[101]_Set-0x197A4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"Foundation[184]-NSMutableString[102]_Set-0x197A8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"Foundation[184]-NSNumber[103]_Set-0x197AC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"Foundation[184]-NSString[104]_Set-0x197B0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"Foundation[184]-NSURL[105]_Set-0x197B4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIApplication[106]_Set-0x197B8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIButton[107]_Set-0x197BC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIColor[108]_Set-0x197C0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIFont[109]_Set-0x197C4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIImage[110]_Set-0x197C8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UILabel[111]_Set-0x197CC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UINavigationBar[112]_Set-0x197D0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UINavigationItem[113]_Set-0x197D4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIScreen[114]_Set-0x197D8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UISegmentedControl[115]_Set-0x197DC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UISlider[116]_Set-0x197E0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UISwitch[117]_Set-0x197E4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UITableView[118]_Set-0x197E8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UITableViewCell[119]_Set-0x197EC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIView[120]_Set-0x197F0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIWindow[121]_Set-0x197F4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIView[122]_Set-0x197F8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"UIKit[185]-UIApplicationMain[123]_Set-0x197FC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"AudioToolbox[187]-AudioServiceCreateSystemSoundId[124]_Set-0x19800"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"AudioToolbox[187]-AudioServiceDisposeSystemSoundId[125]_Set-0x19804"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"AudioToolbox[187]-AudioServicePlaySystemSound[126]_Set-0x19808"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-AddDf3Vfp[127]_Set-0x1980C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-AddSf3Vfp[128]_Set-0x19810"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-DivDf3Vfp[129]_Set-0x19814"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-DivSf3Vfp[130]_Set-0x19818"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-DivSi3[131]_Set-0x1981C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-EqDf2Vfp[132]_Set-0x19820"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-ExtendSfdF2Vfp[133]_Set-0x19824"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-FixDfsiVfp[134]_Set-0x19828"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-FixSfsiVfp[135]_Set-0x1982C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-FixUnSdfsiVfp[136]_Set-0x19830"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-FloatSiDfVfp[137]_Set-0x19834"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-FloatSiSfVfp[138]_Set-0x19838"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-GtDf2Vpf[139]_Set-0x1983C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-LtDf2Vpf[140]_Set-0x19840"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-ModSi3[141]_Set-0x19844"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-MulDf3Vfp[142]_Set-0x19848"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-MulDi3[143]_Set-0x1984C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-MulSf3Vfp[144]_Set-0x19850"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-NeDf2Vfp[145]_Set-0x19854"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-SubDf3Vfp[146]_Set-0x19858"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-SubSf3Vfp[147]_Set-0x1985C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-TruncDfsf2Vfp[148]_Set-0x19860"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libgcc[191]-UDivDi3[149]_Set-0x19864"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-ACos[150]_Set-0x19868"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-ACosH[151]_Set-0x1986C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-ASin[152]_Set-0x19870"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-ASinH[153]_Set-0x19874"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-ATan[154]_Set-0x19878"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-ATanH[155]_Set-0x1987C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-Cos[156]_Set-0x19880"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-CosH[157]_Set-0x19884"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-Exit[158]_Set-0x19888"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-Log[159]_Set-0x1988C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-Log10[160]_Set-0x19890"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-IrInt[161]_Set-0x19894"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-Pow[162]_Set-0x19898"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-RInt[163]_Set-0x1989C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-Sin[164]_Set-0x198A0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-SinH[165]_Set-0x198A4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-Sqrt[166]_Set-0x198A8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-StrChr[167]_Set-0x198AC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-Tan[168]_Set-0x198B0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-TanH[169]_Set-0x198B4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libSystem[189]-TGamma[170]_Set-0x198B8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libobjc[192]-ObjcEmptyCache[171]_Set-0x198BC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libobjc[192]-ObjcEmptyVTable[172]_Set-0x198C0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libobjc[192]-ObjcMsgSend[173]_Set-0x198C4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libobjc[192]-ObjcMsgSendSuper2[174]_Set-0x198C8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libobjc[192]-ObjcMsgSendStret[175]_Set-0x198CC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"libobjc[192]-ObjcSetProperty[176]_Set-0x198D0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"CoreFoundation[188]-NSArray[177]_Set-0x198D4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"CoreFoundation[188]-NSDictionary[178]_Set-0x198D8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"CoreFoundation[188]-NSMutableArray[179]_Set-0x198DC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"CoreFoundation[188]-NSObject[180]_Set-0x198E0"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"CoreFoundation[188]-NSUserDefaults[181]_Set-0x198E4"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"CoreFoundation[188]-NSObject[182]_Set-0x198E8"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FUNCTION),"CoreFoundation[188]-CfConstantStringReference[183]_Set-0x198EC"); // addresses
            
            // Apis/Frameworks
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[184]_Set-0x197A0:libicucore[190]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[184]_Set-0x197A0:libobjc[192]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[184]_Set-0x197A0:libxml2[193]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[184]_Set-0x197A0:libz[193]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[188]_Set-0x197A0:libgcc[191]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[188]_Set-0x197A0:libSystem[189]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[184]_Set-0x197A0:CoreFoundation[188]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[184]_Set-0x197A0:CFNetwork[215]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[184]_Set-0x197A0:SystemConfiguration[210]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-Foundation[184]_Set-0x197A0:Security[214]"); // addresses
            
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:CoreSurface[216]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:IOKit[200]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:Foundation[184]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:GraphicsServices[196]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:JavaScriptCore[213]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:WebCore[212]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:WebKit[211]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:CoreGraphics[203]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:CoreFoundation[188]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:SystemConfiguration[210]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:ImageIO[209]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:IAP[208]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:AddressBook[207]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:AudioToolBox[187]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:QuartzCore[206]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:SpringBoardServices[205]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:AppSupport[204]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:liblockdown[197]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:libicucore[190]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:libobjc[192]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:libstdc++[196]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:libgcc[191]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-UIKit[185]_Set-0x197B8:libSystem[189]"); // addresses
            
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreGraphics[186]_Set-0x000000:CoreGraphics[203]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreGraphics[186]_Set-0x000000:CoreFoundation[188]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreGraphics[186]_Set-0x000000:libz[195]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreGraphics[186]_Set-0x000000:libicucore[190]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreGraphics[186]_Set-0x000000:libgcc[191]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreGraphics[186]_Set-0x000000:libSystem[189]"); // addresses
            
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-AudioToolbox[187]_Set-0x19800:CoreAudio[202]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-AudioToolbox[187]_Set-0x19800:IOKit[200]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-AudioToolbox[187]_Set-0x19800:CoreFoundation[188]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-AudioToolbox[187]_Set-0x19800:libbsm[199]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-AudioToolbox[187]_Set-0x19800:libstdc++[196]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-AudioToolbox[187]_Set-0x19800:libgcc[191]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-AudioToolbox[187]_Set-0x19800:libSystem[189]"); // addresses
            
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreFoundation[188]_Set-0x198D4:libicucore[190]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreFoundation[188]_Set-0x198D4:libobjc[192]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreFoundation[188]_Set-0x198D4:libz[195]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreFoundation[188]_Set-0x198D4:libgcc[191]"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK),"API-CoreFoundation[188]_Set-0x198D4:libSystem[189]"); // addresses
            
            // Libs
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"Appl-libSystem[189]_Set-0x19868"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"IBM-libicucore[190]_Set-0x000000"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"GNU-libgcc[191]_Set-0x1980C"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"Appl-libobjc[192]_Set-0x198BC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"MIT-libxml2[194]_Set-0x198BC"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"BSD-libz[195]_Set-000000"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"GNU-libstdc++[196]_Set-000000"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"Appl-liblockdown[197]_Set-000000"); // addresses
            applicationAddressValues.add(new Integer(islandSoftwareEngine.APP_VENDOR_LIB),"BSD-libbsm[199]_Set-000000"); // addresses
            
            // PREFERENCES PAGE
            //applicationTranslationMapping.add(new Integer(islandSoftwareEngine.APP_GRID_ORIGIN), "Preferences_3:0");
            
            // Hard Coded App is UniApp:TouchCalc usually gotten through UniApp Service
            applicationDetails.put("Description","iPod Touch Calc"); // Translator
            // Applicatoin Flags
            applicationDetails.put("NeedsAuthentication",new Boolean(false));
            applicationDetails.put("UsesTranslator", new Boolean(true)); // Translator
            applicationDetails.put("UsesAddresses", new Boolean(true)); // Addresses
            applicationDetails.put("DefinesGui", new Boolean(true)); // Gui
            applicationDetails.put("HasResources", new Boolean(true)); // Resources
            applicationDetails.put("HasLayouts", new Boolean(true)); // Layouts
            applicationDetails.put("HasProcOps", new Boolean(true)); // Processor
            applicationDetails.put("RunsOnLocal", new Boolean(true)); // Flag to download and save when aquired
            applicationDetails.put("RunOnline", new Boolean(false)); // Flag to save nothing when aquired
            applicationDetails.put("RunsOnlinks", new Boolean(false)); // Flag to save yatta locations when aquired
            
            // Locations can be pointers to gateways, xml files, dbs, or kbs
            /**
             * Local Dev Server /
             */
            applicationDetails.put("AuthenticationLocation","");  
            //applicationDetails.put("ResourcesLocation","http://ekendotech.net/Downloads/UniApp/TouchCalc_Res.xml");
            //applicationDetails.put("AddressesLocation","http://ekendotech.net/Downloads/UniApp/TouchCalc_Addr.xml");
            applicationDetails.put("OperationsLocation","http://ekendotech.net/Downloads/UniApp/TouchCalc_Proc.xml");
            //applicationDetails.put("TranslationLocation","http://ekendotech.net/Downloads/UniApp/TouchCalc_Trans.xml"); 
            
            /**
             * UniApp Demo Server 
             */
            applicationDetails.put("AuthenticationLocation","");  
            applicationDetails.put("ResourcesLocation","http://76.12.235.118/GetXml.aspx?XID=dabba2a3-2090-4b67-abaf-f1531168a099");
            applicationDetails.put("AddressesLocation","http://76.12.235.118/GetXml.aspx?XID=3b215a8c-4d43-4f6c-859a-934db093469e");
            //applicationDetails.put("OperationsLocation","http://76.12.235.118/GetXml.aspx?XID=087b2162-7409-4fa8-bb0d-946017db3f3b");
            applicationDetails.put("TranslationLocation","http://76.12.235.118/GetXml.aspx?XID=a67ebc69-51b8-43d7-a06e-90e3e3d7d903");
            
            
            // Actual Mapping contents
            applicationDetails.put("TranslationMapping",applicationTranslationMapping);
            applicationDetails.put("ResourcesList",applicationResourceValues);
            applicationDetails.put("OperationsMapping", applicationOperationMapping);
            applicationDetails.put("AddressList", applicationAddressValues);
            applicationDetails.put("LoginName","");
            applicationDetails.put("LoginPass","");
            
            availableApplications.put("Touch-Calculator",applicationDetails);
        
            profileSettings.put("PrivateKey",PRIVATE_KEY);
            profileSettings.put("PublicKey",PUBLIC_KEY);
            profileSettings.put("ResourceLocation",LOCATION);// local?
            profileSettings.put("CryptoAlgo","ECKeyPair");
            profileSettings.put("PassWord","Toy3xJayla6xGaybe10x!");
            profileSettings.put("Integrators",defaultIntegrators);
            profileSettings.put("Providers",defaultProviders);
            profileSettings.put("Vendors",defaultVendors);
            profileSettings.put("UltraPeers",defaultUltraPeers);
            profileSettings.put("Peers",defaultPeers);
            profileSettings.put("DisplayMethod","Grid");
            
            userDetails.put("UserName","KenYahhTah");
            userDetails.put("AvailableApplications", availableApplications);
            userDetails.put("AvailableServices",availableServices);
            userDetails.put("AvailableSubscriptions", availableServices);
            userDetails.put("ProfileSettings", profileSettings);
            userDetails.put("DefaultResourceFolder","HomeIslandData");
            userDetails.put("DefaultResourceRoot","file:////");
        }
        catch(Exception e)
        {
            if(debug)
            {
                System.out.println("Issues loading the newIsland:"+e.toString());
            }
        }
    } 

    /* Members -----------------------------------------------------------------------------------------------------------*/
    // Screen Thread status
    private boolean _islandLinksIsRunning;
    private boolean _homeIslandIsRunning;
    private boolean _applicationIsRunning;
    
    // Island Links Flags
    private boolean _userDetailsLoaded;
    private boolean _profileLoaded; 
    private boolean _integratorInfoLoaded;
    private boolean _coreInfoLoaded;
    private boolean _vendorInfoLoaded;
    private boolean _ultraPeerInfoLoaded;
    private boolean _peerInfoLoaded;
    private boolean _applicationLoaded;
    private boolean _serviceLoaded;
    private boolean _subscriptionLoaded;
    
    // GUI initializations
    private MainScreen _mainScreen; // uses HomeIslandGUIScreen
    private StringBuffer _message = new StringBuffer();
   
    private class HomeIslandGUIScreen extends MainScreen
    {
        /* custom overlay button */
        public class YattaButton extends RichTextField 
        {
            private String buttonLabel;
            private int innerWidth;
            private String innerSpaces;
            private int innerHeight;
            private boolean _debug;
            private int thisColor;
            
            public YattaButton(int InnerWidth, String InnerName, int InnerHeight, String ButtonSpaces, boolean Debug, int ThisColor) 
            {   
                super(ButtonSpaces, RichTextField.FOCUSABLE|RichTextField.READONLY);   
                     
                buttonLabel = InnerName;
                innerWidth = InnerWidth;
                innerSpaces = ButtonSpaces;
                buttonLabel = InnerName;
                innerHeight = InnerHeight;
                thisColor = ThisColor;
                _debug = Debug;
            }
            
            public void setText(String text)
            {
                innerSpaces = text;
                invalidate();
            }
    
            public void layout(int width, int height) 
            {
                if(_debug)
                {
                    System.out.println("[BUTTON PRINT] The name is: " + buttonLabel);
                    System.out.println("[BUTTON PRINT] The label is: |" + innerSpaces + "| - (Pipes added to outside)");
                    System.out.println("[BUTTON PRINT] The Width is: " + String.valueOf(innerWidth));
                    System.out.println("[BUTTON PRINT] The Height is: " + String.valueOf(innerHeight));
                }
                setExtent(innerWidth, innerHeight);
            }
    
            public String getButtonName()
            {
                return buttonLabel;
            }
            
            public void paint(Graphics tGraph) 
            {
                if(drawRed)
                    tGraph.setColor(Color.CRIMSON);
                else if(thisColor > -1)
                    tGraph.setColor(thisColor);
                tGraph.setFont(yattaFont);
                tGraph.drawText(innerSpaces,0,0,tGraph.ELLIPSIS);
                if(_debug)
                {
                    System.out.println("[BUTTON PRINT] Painting : " + buttonLabel);
                }
            }
            
            protected boolean navigationClick(int status, int time) 
            {
                if(_debug)
                {
                    System.out.println("[YATTA CLICK] The button clicked was: " + getButtonName());
                }
                fieldChangeNotify(1);
                
                return true;
            }

            public boolean navigationMovement(int dx, int dy, int status, int time)
            {
                /*
                _overFlow.invalidate();
                _componentsManager.invalidate();
                _graphicsManager.invalidate();
                */
                return false;
            }

        }; 
        
        /* Custom Menu Item */
        private class ApplicationPreferenceItem extends MenuItem
        {
            String preferenceName = "";
            
            private ApplicationPreferenceItem(String name, int ordinal, int priority)
            {
                super(name,ordinal,priority);
            
                preferenceName = name;
                
            }
            
            public void run()
            {
                if ( _applicationIsRunning )
                {
                    // Load Main App thread
                    //synchronized(getLockObject())
                    {
                        // Set selected option
                        _islandApplicationManager.ApplicationEngine.PerformOperationForEvent(islandSoftwareEngine.USER_DEVICE_MENU_EVENT,preferenceName);
                    
                        if(_debug)
                        {
                            System.out.println("[AppPrefItem-INFO] menu clicked");
                        }
                        
                        updateDisplay("",DISPLAY_GUI_COMPONENTS);
                        updateDisplay("",DISPLAY_GRID_OVERLAYS);
                        updateDisplay("",DISPLAY_GRAPHIC_OBJECTS);
                    }
                }
            }
        };
        
        // default menus
        private MenuItem _getServicesList;
        private MenuItem _loadSubscriptions;
        private MenuItem _loadProfile;  
        private MenuItem _getNewApps;
        private MenuItem _shareResources;
        private MenuItem _startLoadedApp;
        private ApplicationPreferenceItem _getAppPreferences;
        
        // default layouts
        private VerticalFieldManager _graphicsManager;
        private VerticalFieldManager _componentsManager;
        // flow fielsd man
        private DialogFieldManager _dialogManager;
        private FlowFieldManager _overFlow;
        
        // manager tracker
        private Stack _layoutObjectStack;
        private Stack _graphicObjectStack;
        private Stack _componentObjectStack;
        private Stack _dialogObjectStack;
        private Stack _overFlowObjectStack;
    
        // flags
        private boolean _needsIslandLinks;
   
        // default fields
        private RichTextField _rtf = new RichTextField(FIELD_HCENTER);
        private EditField _bef = new EditField(FIELD_HCENTER);
        private YattaButton _gridBox = null;
        
        // default dialog
        private Dialog _d;
        private boolean _debug;
         
        // display window
        //private int deviceWidth = Display.getWidth();
        //private int deviceHeight = Display.getHeight();

        // thread handles
        private HomeIslandThread _islandResourceManager; // 
        private IslandLinksConnectThread _islandConnectionManager;
        private IslandApplicationThread _islandApplicationManager;
        
        /**
         * Constructs a new main screen instance with provided style and creates
         * the necessary menu items.
         * 
         * @param style Style(s) for this new screen.
         */
        private HomeIslandGUIScreen( long style ) 
        {
            super( style );
            
            if(debug)
            {
                this._debug = true;
            }
            
            
            
            _shareResources = new MenuItem("Share Resources" , 1010, 10)
            {
                public void run()
                {
                    // Resources Dialog for Memory, processor, and IP Address
                    synchronized(getLockObject())
                    {
                        if ( !_homeIslandIsRunning )
                        {
                            _homeIslandIsRunning = true;
                            IslandResourceState = islandUserResource.USER_SERVICES;
                            _islandResourceManager = new HomeIslandThread();
                            _islandResourceManager.start();
                        }
                    }
                }
            };
            
            _startLoadedApp =  new MenuItem("Start Current App" , 1009, 9)
            {
                public void run()
                {
                    // Load Default Service List from Default Provider Service
                    synchronized(getLockObject())
                    {
                        // needs islandLinks
                        if(_needsIslandLinks)
                        {
                            if ( !_islandLinksIsRunning )
                            {
                                _islandLinksIsRunning = true;
                                _islandConnectionManager = new IslandLinksConnectThread();
                                _islandConnectionManager.start();
                            }
                        }
                        
                        // Load Main App thread
                        synchronized(getLockObject())
                        {
                            if ( !_applicationIsRunning )
                            {
                                _applicationIsRunning = true;
                                _islandApplicationManager = new IslandApplicationThread();
                                _islandApplicationManager.start();
                            }
                        }
                    }
                }
            };
            
            _getNewApps = new MenuItem("Get New Apps", 1008, 8)
            {
                public void run()
                {
                    // Load New Applications List from Provider?
                    synchronized(getLockObject())
                    {
                        
                        if ( !_homeIslandIsRunning )
                        {
                            _homeIslandIsRunning = true;
                            IslandResourceState = islandUserResource.USER_APPLICATIONS;
                            _islandResourceManager = new HomeIslandThread();
                            _islandResourceManager.start();
                        }
                        
                    }
                }
            };
            
            _loadProfile = new MenuItem("Edit Island Profile", 1007, 7)
            {
                public void run()
                {
                    // Dialog for default Island Locations, Private & Public Key, Settings   
                    // Resources Dialog for Memory, processor, and IP Address
                    synchronized(getLockObject())
                    {
                        if ( !_homeIslandIsRunning )
                        {
                            _homeIslandIsRunning = true;
                            IslandResourceState = islandUserResource.USER_PROFILE;
                            _islandResourceManager = new HomeIslandThread();
                            _islandResourceManager.start();
                        }
                    }
                }
                
            };
            
            _loadSubscriptions = new MenuItem("Manage Subscriptions", 1006, 6)
            {
                public void run()
                {
                    // Dialog to list Services to subscribe or cancel
                    synchronized(getLockObject())
                    {
                        if ( !_homeIslandIsRunning )
                        {
                            _homeIslandIsRunning = true;
                            IslandResourceState = islandUserResource.USER_SUBSCRIPTIONS;
                            _islandResourceManager = new HomeIslandThread();
                            _islandResourceManager.start();
                        }
                    }
                }
            };
            
            _getServicesList = new MenuItem("Get Services", 1005, 5)
            {
                public void run()
                {
                    // Load New Applications List from Provider?
                    synchronized(getLockObject())
                    {
                        if ( !_islandLinksIsRunning )
                        {
                            _islandLinksIsRunning = true;
                            _islandConnectionManager = new IslandLinksConnectThread();
                            _islandConnectionManager.start();
                        }
                    }
                }
            };
            
            _graphicsManager = new VerticalFieldManager(Manager.NO_VERTICAL_SCROLL | Manager.NO_VERTICAL_SCROLLBAR )
            {            
                public void paint(Graphics graphics)
                {
                    graphics.clear();
                    int tmpColor;
                    boolean drewTextbox = false;
                    
                    // paint custom graphics
                    if(_applicationIsRunning)
                    {
                        try
                        {
                            if(_islandApplicationManager != null)
                            {
                                if(_debug)
                                {
                                    System.out.println("[graphicsMan-INFO] appMan is not null");
                                }
                
                                if(_islandApplicationManager.ApplicationEngine!=null)
                                {
                                    if(_islandApplicationManager.ApplicationEngine.HasGraphics()&&
                                        (_islandApplicationManager.ApplicationEngine.GetGraphicObjects()!=null)&&
                                        (_islandApplicationManager.ApplicationEngine.GetEngineState() == islandSoftwareEngine.APP_ENGINE_STARTED))
                                    {
                                        
                                        if(_debug)
                                        {
                                            ClearDebugText();
                                        }
                                        
                                        if(!_islandApplicationManager.currentGrid.equals(""))
                                        {
                                            if(_layoutObjectStack == null)
                                            {
                                                _layoutObjectStack = new Stack();
                                            }

                                            else if (!_layoutObjectStack.isEmpty())
                                            {
                                                _layoutObjectStack.removeAllElements();
                                            }

                                            DisplayGraphicGridObjects(graphics);
                                        }
                                        else
                                        {
                                            DisplayGraphicGuiObjects(graphics);
                                        }
                                    }
                                }
                            }
                        }
                        catch(Exception e)
                        {
                            if(_debug)
                            {
                                System.out.println("[ERROR] problems painting using the graphics manager:"+e.toString());
                            }
                        }
                    }                        
                    
                    // paint components etc
                    super.paint(graphics);                           
                }    
                
                public void navigationClick()
                {
                    if(_debug)
                    {
                        System.out.println("[INFO] graphics screen nav clicked");
                    }
                }
                
            };
            
            //| USE_ALL_WIDTH | VERTICAL_SCROLL | VERTICAL_SCROLLBAR 
            _componentsManager = new VerticalFieldManager(USE_ALL_WIDTH | USE_ALL_HEIGHT )
            {
                public void paint(Graphics graphics)
                {
                    // initialize tracker
                    if(_componentObjectStack == null)
                    {
                        _componentObjectStack = new Stack();
                    }
                    
                    if(_applicationIsRunning)
                    {
                        try
                        {
                            PopulateComponentStack();
                        }
                        catch(Exception ex)
                        {
                            if(_debug)
                            {
                                System.out.println("[ERROR] problems painting using the components manager:"+ex.toString());
                            }
                        }
                    }                            
                    super.paint(graphics);
                 } 
            };

            
            _overFlow = new FlowFieldManager(FlowFieldManager.USE_ALL_WIDTH|FlowFieldManager.USE_ALL_HEIGHT)
            {
                public void paint(Graphics graphics)
                {
                    // initialize tracker
                    if(_overFlowObjectStack == null)
                    {
                        _overFlowObjectStack = new Stack();
                    }
                    
                    if(_applicationIsRunning)
                    {
                        if(_debug)
                        {
                            System.out.println("[INFO] painting over flow items");
                        }
                        
                        try
                        {
                            if(_layoutObjectStack !=null)
                            {
                                for(Enumeration e = _layoutObjectStack.elements(); e.hasMoreElements();)
                                {
                                    Hashtable attributes = (Hashtable) e.nextElement();
                                    
                                    if(attributes!=null)
                                    {
                                        String name = (String) attributes.get("Name");
                                        
                                        //YattaButton yButton = (YattaButton) attributes.get("Overlay");
                                        //AddButtonToOverflow(width.doubleValue(),height.intValue(),spaceW.intValue(),name,text);
                                        
                                        if(!_overFlowObjectStack.contains(name))
                                        {
                                            YattaButton yButton = (YattaButton) attributes.get("Overlay");
                                            _overFlow.add(yButton); //Causes NullPointer Exception
                                            //_overFlow.add(new RichTextField("                                                  ",RichTextField.FOCUSABLE|RichTextField.READONLY));
                                            _overFlowObjectStack.addElement(name);
                                            if(_debug)
                                            {
                                                System.out.println("[INFO] added the button:"+name);
                                            }
                                        }
                                        else
                                        {
                                            if(_debug)
                                            {
                                                System.out.println("[INFO] already had the button:"+name);
                                            }
                                        }
                                        
                                        
                                    }
                                }
                                
                                _layoutObjectStack = null;
                                _loadedLabels = true;
                            }
                        }
                        catch(Exception Ex)
                        {
                            if(_debug)
                            {
                                System.out.println("[ERROR] problems painting using the overFlow manager:"+Ex.toString());
                            }
                        }
                    }
                
                    super.paint(graphics);
                    
                }
                
                public void navigationClick()
                {
                    if(_debug)
                    {
                        System.out.println("[INFO] overFlow manager clicked");
                    }
                }
               
            };
            
            
            _dialogManager = new DialogFieldManager(Manager.NO_VERTICAL_SCROLL | Manager.NO_VERTICAL_SCROLLBAR);
            
            if(_islandResourceManager == null)
            {
                _islandResourceManager = new HomeIslandThread();
                //_islandResourceManager.
            }
            
            // uses more resources but looks waaaaaaay better resizes automagically
            if(_islandResourceManager.GetUserDisplayDefault().equals("Grid"))
            {
                _overFlow.add(_rtf);
            
                _graphicsManager.add(_overFlow);
            }
            
            // old skool paint at point
            if(_islandResourceManager.GetUserDisplayDefault().equals("Gui"))
            {
                _componentsManager.add(_rtf); 
            
                _graphicsManager.add(_componentsManager);
            }
            
            /* probably need app categories [data entry, content update, utility]*/
            add(_graphicsManager);
                
        }
        
        public void ClearDebugText()
        {
            if(_islandResourceManager.GetUserDisplayDefault().equals("Gui"))
            {
                if(_componentsManager !=null)
                {
                    if(_rtf.getManager() == _componentsManager)
                    {
                        if(!_rtf.getText().equals(""))
                        {
                            _rtf.setText("");
                        }       
                     }
                }
            }
            
            if(_islandResourceManager.GetUserDisplayDefault().equals("Grid"))
            {
                if(_overFlow !=null)
                {
                    if(_rtf.getManager() == _overFlow)
                    {
                        if(!_rtf.getText().equals(""))
                        {
                            _rtf.setText("");
                        }       
                     }
                }
            }
        }
        
        
        private void PopulateComponentStack()
        {
            try
            {
                if(_componentObjectStack == null)
                {
                    _componentObjectStack = new Stack();
                    _componentObjectStack.addElement(_rtf);
                }   
                else if(_componentObjectStack.isEmpty())
                {
                    _componentObjectStack.addElement(_rtf);
                }
                
                if(_islandApplicationManager != null)
                {
                    if(_islandApplicationManager.ApplicationEngine!=null)
                    {
                        if(_islandApplicationManager.ApplicationEngine.HasComponents()&&
                            (_islandApplicationManager.ApplicationEngine.GetComponentObjects()!=null))
                        {
                            //this.deleteAll(); // blanks almost everything
                            
                            if(!_islandApplicationManager.currentGrid.equals(""))
                            {
                                islandSoftwareEngine.GridScreenDetails appGridDetails = (islandSoftwareEngine.GridScreenDetails) _islandApplicationManager.ApplicationEngine.GetDisplayGridFromName(_islandApplicationManager.currentGrid);
                                
                                // Get Loaded Layouts for Screen
                                Enumeration appLayouts = appGridDetails.GetScreenLayouts(appGridDetails.ScreenName);
                                
                                for(Enumeration en = appLayouts; en.hasMoreElements();)
                                {
                                    islandSoftwareEngine.DisplayLayoutDetails appLayoutDetails = (islandSoftwareEngine.DisplayLayoutDetails) en.nextElement();
                                
                                    // enumerate object container (GuiGraphicDetails)
                                    for(Enumeration e = appLayoutDetails.GetLayoutComponents().elements(); e.hasMoreElements();)
                                    {
                                        // paint graphics
                                        islandSoftwareEngine.GraphicGuiDetails appComponentDetails = (islandSoftwareEngine.GraphicGuiDetails) e.nextElement();
                                        
                                        if(appComponentDetails !=null)
                                        {
                                            
                                           if(appComponentDetails.ComponentsObject instanceof BasicEditField)
                                            {
                                                BasicEditField bef = (BasicEditField) appComponentDetails.ComponentsObject;
                                                SeparatorField sf = new SeparatorField();
                                                    
                                                if(_debug)
                                                {
                                                    if(appComponentDetails.ObjectColor == Color.BLACK)
                                                    {
                                                        System.out.println("[INFO] component color is BLACK");
                                                    }
                                                    
                                                    if(appComponentDetails.ObjectColor == Color.WHITE)
                                                    {
                                                        System.out.println("[INFO] component color is WHITE");
                                                    }
                                                }
                                                
                                                if(appComponentDetails.Event == null)
                                                {
                                                    appComponentDetails.Event = "";
                                                }
                                                            
                                                if(!appComponentDetails.Event.equals("Removal"))
                                                {
                                                    if(appComponentDetails.Events == null)
                                                    {
                                                        appComponentDetails.Events = new Stack();
                                                    }
                                                    
                                                    if((!appComponentDetails.Events.contains("Draw_Precise"))&&
                                                        (!appComponentDetails.Events.contains("Draw:Precise")))
                                                    { 
                                                        if(!_componentObjectStack.contains(bef))
                                                        {
                                                            _componentObjectStack.addElement(bef);    
                                                                
                                                            _componentObjectStack.addElement(sf);
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if(_componentObjectStack.contains(bef))
                                                        {
                                                            _componentObjectStack.removeElement(bef);
                                                            delete(bef);
                                                            
                                                            _componentObjectStack.removeElement(sf);
                                                            delete(sf);
                                                        }
                                                        
                                                    }
                                                }
                                                
                                                if(appComponentDetails.Event.equals("Removal"))
                                                {
                                                    if(_componentObjectStack.contains(bef))
                                                    {
                                                        if(!bef.getText().equals(""))
                                                        {
                                                            bef.setText("");
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            if(appComponentDetails.ComponentsObject instanceof RichTextField)
                                            {
                                                RichTextField rtf = (RichTextField) appComponentDetails.ComponentsObject;
                                                SeparatorField sf = new SeparatorField();
                                                
                                                if(_debug)
                                                {
                                                    if(appComponentDetails.ObjectColor == Color.BLACK)
                                                    {
                                                        System.out.println("[INFO] component color is BLACK");
                                                    }
                                                    
                                                    if(appComponentDetails.ObjectColor == Color.WHITE)
                                                    {
                                                        System.out.println("[INFO] component color is WHITE");
                                                    }
                                                }
                                                        
                                                if(appComponentDetails.Event == null)
                                                {
                                                    appComponentDetails.Event = "";
                                                }
                                                            
                                                if(!appComponentDetails.Event.equals("Removal"))
                                                {
                                                    if(appComponentDetails.Events == null)
                                                    {
                                                        appComponentDetails.Events = new Stack();
                                                    }
                                                    
                                                    if((!appComponentDetails.Events.contains("Draw_Precise"))&&
                                                        (!appComponentDetails.Events.contains("Draw:Precise")))
                                                    {    
                                                        if(!_componentObjectStack.contains(rtf))
                                                        {
                                                            _componentObjectStack.addElement(rtf);
                                                                
                                                            if(!rtf.getText().equals(""))
                                                            {
                                                                _componentObjectStack.addElement(sf);
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if(_componentObjectStack.contains(rtf))
                                                        {
                                                            _componentObjectStack.removeElement(rtf);
                                                            delete(rtf);
                                                        }
                                                    }
                                                }  
                                                
                                                if(appComponentDetails.Event.equals("Removal"))
                                                {
                                                    if(_componentObjectStack.contains(rtf))
                                                    {
                                                        if(!rtf.getText().equals(""))
                                                        {
                                                            rtf.setText("");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            catch(Exception ex)
            {
                if(_debug)
                {
                    System.out.println("[ERROR] problems painting using the components manager:"+ex.toString());
                    ex.printStackTrace();
                }
            }
        }
        
        public void DisplayGraphicGridObjects(Graphics graphics)
        {
            String buttonSpaces = "";
            String oldButtonName = "";
            boolean drewBackground = false;
            double currentX = 0;
            double currentY = 0;
            int fontWidth = 0;
            int innerWidth = 0;
            
    
                    
            islandSoftwareEngine.GridScreenDetails appGridDetails = (islandSoftwareEngine.GridScreenDetails) _islandApplicationManager.ApplicationEngine.GetDisplayGridFromName(_islandApplicationManager.currentGrid);
                                            
            // set th grid color
            graphics.setColor(appGridDetails.GridColor);
                                            
            if(!drewBackground)
            {
                if(_debug)
                {
                    if(appGridDetails.GridColor ==Color.BLACK)
                    {
                        System.out.println("[Drawing Background]The layout color here is: Black");
                    }
                                                        
                    if(appGridDetails.GridColor == Color.WHITE)
                    {
                        System.out.println("[Drawing Background]The layout color here is: White");
                    }
                }
                                                
                // paint the grid background area
                graphics.fillRect(appGridDetails.GetGridOriginX(), appGridDetails.GetGridOriginY(), appGridDetails.GetGridWidth(),appGridDetails.GetGridHeight());
                drewBackground = true;
            }

            if(_debug)
            {
                System.out.println("[INFO] this is the current screen name:"+appGridDetails.ScreenName);
                System.out.println("[INFO] this is the grid width:"+appGridDetails.GetGridWidth());
                System.out.println("[INFO] this is the grid height:"+appGridDetails.GetGridHeight());
            }  
                                            
            // Get Loaded Layouts for Screen
            Enumeration appLayouts = appGridDetails.GetScreenLayouts(appGridDetails.ScreenName);
                                            
            for(Enumeration en = appLayouts; en.hasMoreElements();)
            {
                islandSoftwareEngine.DisplayLayoutDetails appLayoutDetails = (islandSoftwareEngine.DisplayLayoutDetails) en.nextElement();
                                                
                 if(_debug)
                 {
                    System.out.println("[INFO] this is the current layout name:"+appLayoutDetails.Name);
                    System.out.println("[INFO] this is the current layout details placementX&placementY:"+appLayoutDetails.GetPlacementX()+"&"+appLayoutDetails.GetPlacementY());
                    System.out.println("[INFO] this is the layout height and wdth:"+appLayoutDetails.LayoutHeight+"&"+appLayoutDetails.LayoutWidth);
                 }                

                 // Layout value conversion
                 Double dLPx = new Double(appLayoutDetails.GetPlacementX());
                 Double dLPy = new Double(appLayoutDetails.GetPlacementY());
                 Double dLPw = new Double(appLayoutDetails.LayoutWidth);
                 Double dLPh = new Double(appLayoutDetails.LayoutHeight);
                                                
                 // get Loaded Graphics
                 Vector appGraphics = appLayoutDetails.GetLayoutGraphics();
                                                
                 // set the layout color
                 graphics.setColor(appLayoutDetails.LayoutColor);
                                            
                 if(_debug)
                 {
                    if(appLayoutDetails.LayoutColor ==Color.BLACK)
                    {
                        System.out.println("[INFO]The layout color here is: Black");
                    }
                                                    
                    if(appLayoutDetails.LayoutColor == Color.WHITE)
                    {
                        System.out.println("[INFO]The layout color here is: White");
                    }
                 }
                                                
                 // fil the layout background area
                 if(graphics.getColor()==Color.WHITE)
                 {
                    graphics.fillRect(dLPx.intValue(), dLPy.intValue(), dLPw.intValue(),dLPh.intValue());
                 }
                                                
                 // enumerate object container (GuiGraphicDetails)
                 for(Enumeration e = appGraphics.elements(); e.hasMoreElements();)
                 {
                    // paint graphics
                    islandSoftwareEngine.GraphicGuiDetails appGraphicDetails = (islandSoftwareEngine.GraphicGuiDetails) e.nextElement();
                                                    
                    if(appGraphicDetails !=null)
                    {
                                                        
                        // Graphic value conversion
                        Double dPx = new Double(appGraphicDetails.PlacementX());
                        Double dPy = new Double(appGraphicDetails.PlacementY());
                        Double dLx = new Double(appGraphicDetails.LocationX());
                        Double dLy = new Double(appGraphicDetails.LocationY());
                                                        
                        if(appGraphicDetails.GraphicsObject instanceof Bitmap)
                        {
                            Bitmap bmp = (Bitmap) appGraphicDetails.GraphicsObject;
                                                            
                            if(bmp!=null)
                            {
                                if(_debug)
                                {
                                    System.out.println("[INFO] this is the current details name:"+appGraphicDetails.Name);
                                                                    
                                    if(appGraphicDetails.Location!=null)
                                    {
                                        System.out.println("[INFO] this is the current details locationX&locationY:"+appGraphicDetails.LocationX()+"&"+appGraphicDetails.LocationY());
                                    }
                                                                    
                                    if(appGraphicDetails.Placement!=null)
                                    {
                                        System.out.println("[INFO] this is the current details placementX&placementY:"+appGraphicDetails.PlacementX()+"&"+appGraphicDetails.PlacementY());
                                    }
                                 }
                                                                    
                                 if(appGraphicDetails.Name.equals("Background_Image"))
                                 {   
                                    if(_debug)
                                    {
                                        System.out.println("[ERROR] Trying to Draw a Background Image!!!");
                                    }
                                 }
                                 else
                                 {
                                    // Not a Background Image
                                    if((appGraphicDetails.Location!=null)&&(appGraphicDetails.Placement!=null))
                                    {
                                        if(appGraphicDetails.Event == null)
                                        {
                                            appGraphicDetails.Event = "";
                                        }
                                                                        
                                        if(!appGraphicDetails.Event.equals("Removal"))
                                        {
                                            if(_debug)
                                            {
                                                System.out.println("[INFO] img width and height:"+bmp.getWidth()+"&"+ bmp.getHeight());
                                                System.out.println("[INFO] appGraphicDetails width and height:"+appGraphicDetails.width+"&"+ appGraphicDetails.height);
                                            }
                                                                            
                                            //Now we draw the bitmap:
                                            int useThisWidth = ((int) (appGraphicDetails.width + 0.5F)) - 2;
                                            int useThisHeight = ((int) (appGraphicDetails.height + 0.5F)) - 2;
                                            int textHCenter = ((int) ((useThisWidth / 2) +  + 0.5F)) + dPx.intValue();
                                            int textVCenter = ((int) ((useThisHeight / 2) + 0.5F)) + dPy.intValue();
                                            String buttonName = appGraphicDetails.Name;
                                                                                
                                            //Before drawing the bitmap, we need to create a new sized image to match our placement box:
                                            Bitmap sizedMap = islandSoftwareEngine.resizeBitmap(bmp,useThisWidth,useThisHeight);
                                                                            
                                            graphics.drawBitmap(dPx.intValue(),dPy.intValue(),useThisWidth,useThisHeight,sizedMap, dLx.intValue(),dLy.intValue());
                                                                            
                                            //Next we add text if warranted:
                                            if(appGraphicDetails.HasText)
                                            {
                                                String thisText =  appGraphicDetails.ObjectText.replace('\n',' ').trim();
                                                                                
                                                if(_debug)
                                                {
                                                    System.out.println("[TOLA] The Graphic Text is: " + thisText);
                                                    System.out.println("[TOLA] The X,Y for this button is: " + String.valueOf(dPx) + "," + String.valueOf(dPy));
                                                    if(appGraphicDetails.ObjectColor==Color.BLACK)
                                                    {
                                                        System.out.println("[INFO] Current Graphic Object Color is: BLACK");
                                                    }
                                                                                    
                                                    if(appGraphicDetails.ObjectColor==Color.WHITE)
                                                    {
                                                        System.out.println("[INFO] Current Graphic Object Color is: WHITE");
                                                    } 
                                                }
                                                                                
                                                Font newFont = Font.getDefault().derive(Font.BOLD,((int) ((useThisHeight / 2) + 0.5F)));
                                                graphics.setColor(Color.WHITE);
                                                graphics.setFont(newFont);
                                                                                
                                                if(thisText.indexOf("\\u") > -1)
                                                {
                                                    try
                                                    {
                                                        if(_debug)
                                                        {
                                                            System.out.println("Base Value to be converted: "+ thisText);
                                                        }
                                                        
                                                        String newValue = "";
                                                        String convertValue = thisText.substring(thisText.indexOf("\\u")+2,thisText.indexOf(";"));
                                                        //We need to convert the value to unicode
                                                        short valueAsShort = Short.parseShort(convertValue.trim(),16);
                                                        newValue = String.valueOf((char)valueAsShort);
                                                        
                                                        while(thisText.length() > thisText.indexOf(";")+1)
                                                        {
                                                            thisText = thisText.substring(thisText.indexOf(";") + 1);
                                                            convertValue = thisText.substring(thisText.indexOf("\\u")+2,thisText.indexOf(";"));
                                                            //We need to convert the value to unicode
                                                            valueAsShort = Short.parseShort(convertValue.trim(),16);
                                                            newValue += String.valueOf((char)valueAsShort);
                                                        }
                                                        
                                                        buttonName = newValue;
                                                        
                                                        if(_debug)
                                                        {
                                                            System.out.println("Converted Value: "+ newValue);
                                                        }
                                                        graphics.drawText(newValue, dPx.intValue(),textVCenter,DrawStyle.HCENTER | DrawStyle.VCENTER, useThisWidth);
                                                    }
                                                    catch(Exception exU)
                                                    {
                                                        if(_debug)
                                                        {
                                                            System.out.println("[ERROR] Converting to Unicode failed: "+exU.toString());
                                                            exU.printStackTrace();
                                                        }
                                                    }
                                                }
                                                else
                                                {                             
                                                    buttonName = thisText;
                                                    graphics.drawText(thisText, dPx.intValue(),textVCenter,DrawStyle.HCENTER | DrawStyle.VCENTER, useThisWidth);
                                                }
                                                                                
                                                if(_debug)
                                                {
                                                    if(graphics.getColor()==Color.BLACK)
                                                    {
                                                        System.out.println("[INFO] Wrote out text in BLACK");
                                                    }
                                                                                    
                                                    if(graphics.getColor()==Color.WHITE)
                                                    {
                                                        System.out.println("[INFO] Wrote out text in WHITE");
                                                    } 
                                                    
                                                    System.out.println("[INFO] Text Coordinates (X,Y): " + textHCenter + "," + textVCenter);
                                                }
                                            }
                                            else
                                            {
                                                if(_debug)
                                                {
                                                    System.out.println("[INFO] Graphic Has No Text");
                                                }
                                            }
                                                                            
                                            if(!_loadedLabels)
                                            {
                                                //Finally we add our labels
                                                Hashtable OverflowButton = new Hashtable();
                                                useThisWidth += 2;
                                                //useThisHeight += 2;
                                                //We need to draw labels over our buttons - to do so, we need to create a series of managers to handle our layout:
                                                if(yattaFont == null)
                                                    yattaFont = Font.getDefault().derive(Font.PLAIN,((int) ((this.getVisibleHeight() / 12) + 0.5F)));
                                                int spaceWidth = yattaFont.getAdvance(' ');
                                                
                                                if(_layoutObjectStack.isEmpty())
                                                {
                                                    
                                                    //int spacerHeight = this.getVisibleHeight() / 6;  //2 rows...
                                                    int spacerHeight = (int) (((this.getVisibleHeight() / 12) * 1.7)  + 0.5F);  //1.7 rows...

                                                    
                                                    //We need to create a manager to take up the top two rows:
                                                    if(_debug)
                                                    {
                                                        System.out.println("[LABEL PRINT] Adding textFiller button label");
                                                    }
                                                    
                                                    innerWidth = ((int) ((this.getVisibleWidth() - 5) + 0.5F));
                                                    OverflowButton = new Hashtable();
                                                    OverflowButton.put("Name","Textbox");
                                                    fontWidth = ((int) (((this.getVisibleWidth() - 5) / spaceWidth) + 0.5F));;
                                                    buttonSpaces = "";
                                                    while(fontWidth >= 0)
                                                    {
                                                        buttonSpaces += spaceChar;
                                                        fontWidth -= 1;
                                                    }
                                                    
                                                    //OverflowButton.put("Overlay",new YattaButton(innerWidth,"Textbox",spacerHeight,buttonSpaces,_debug));
                                                    //_layoutObjectStack.addElement(OverflowButton);
                                                    
                                                    _gridBox = new YattaButton(innerWidth,"Top Box",spacerHeight,buttonSpaces,_debug,Color.BLACK);

                                                    OverflowButton.put("Overlay",_gridBox);
                                                    _layoutObjectStack.addElement(OverflowButton);
                                                    
                                                }
                                                
                                                
                                                //Next we need to determine what to add into our flow manager:
                                                if(currentX != dPx.doubleValue() && currentY == dPy.doubleValue() && oldButtonName != "")
                                                {
                                                    //There are spaces - anything less than a space who cares?
                                                    if(dPx.doubleValue() - currentX > 1)
                                                    {
                                                        if(dPx.doubleValue() - currentX < 2)
                                                        {
                                                            //In this case, we just assign the space to the old button:
                                                            innerWidth = ((int) ((dPx.doubleValue() - currentX) + 0.5F));
                                                            OverflowButton = new Hashtable();
                                                            OverflowButton.put("Name","old_" + oldButtonName);
                                                            fontWidth = ((int) (((dPx.doubleValue() - currentX) / spaceWidth) + 0.5F));;
                                                            buttonSpaces = "";
                                                            while(fontWidth >= 0)
                                                            {
                                                                buttonSpaces += spaceChar;
                                                                fontWidth -= 1;
                                                            }
                                                            
                                                            OverflowButton.put("Overlay",new YattaButton(innerWidth,oldButtonName,useThisHeight,buttonSpaces,_debug,-1));
                                                            _layoutObjectStack.addElement(OverflowButton);
                                                            
                                                            //We also need to add in the new button label:
                                                            innerWidth = ((int) ((useThisWidth) + 0.5F));
                                                            OverflowButton = new Hashtable();
                                                            OverflowButton.put("Name",buttonName);
                                                            fontWidth = ((int) (((useThisWidth) / spaceWidth) + 0.5F));;
                                                            buttonSpaces = "";
                                                            while(fontWidth >= 0)
                                                            {
                                                                buttonSpaces += spaceChar;
                                                                fontWidth -= 1;
                                                            }
                                                            
                                                            OverflowButton.put("Overlay",new YattaButton(innerWidth,buttonName,useThisHeight,buttonSpaces,_debug,-1));
                                                            _layoutObjectStack.addElement(OverflowButton);
                                                        }
                                                        else
                                                        {
                                                            //We split the difference:
                                                            double halfzies = (dPx.doubleValue() - currentX) / 2;
                                                            //First the old button:
                                                            innerWidth = ((int) ((halfzies) + 0.5F));
                                                            OverflowButton = new Hashtable();
                                                            OverflowButton.put("Name","old_" + oldButtonName);
                                                            fontWidth = ((int) (((halfzies) / spaceWidth) + 0.5F));;
                                                            buttonSpaces = "";
                                                            while(fontWidth >= 0)
                                                            {
                                                                buttonSpaces += spaceChar;
                                                                fontWidth -= 1;
                                                            }
                                                            
                                                            OverflowButton.put("Overlay",new YattaButton(innerWidth,oldButtonName,useThisHeight,buttonSpaces,_debug,-1));
                                                            _layoutObjectStack.addElement(OverflowButton);
                                                            
                                                            //We also need to add in the new button label:
                                                            innerWidth = ((int) ((useThisWidth + halfzies) + 0.5F));
                                                            OverflowButton = new Hashtable();
                                                            OverflowButton.put("Name",buttonName);
                                                            fontWidth = ((int) (((useThisWidth + halfzies) / spaceWidth) + 0.5F));;
                                                            buttonSpaces = "";
                                                            while(fontWidth >= 0)
                                                            {
                                                                buttonSpaces += spaceChar;
                                                                fontWidth -= 1;
                                                            }
                                                            
                                                            OverflowButton.put("Overlay",new YattaButton(innerWidth,buttonName,useThisHeight,buttonSpaces,_debug,-1));
                                                            _layoutObjectStack.addElement(OverflowButton);
                                                        }
                                                    }
                                                    else
                                                    {
                                                        //We add the button like normal:
                                                        OverflowButton = new Hashtable();
                                                        OverflowButton.put("Name",buttonName);
                                                        fontWidth = ((int) ((useThisWidth / spaceWidth) + 0.5F));;
                                                        buttonSpaces = "";
                                                        while(fontWidth >= 0)
                                                        {
                                                            buttonSpaces += spaceChar;
                                                            fontWidth -= 1;
                                                        }
                                                        OverflowButton.put("Overlay",new YattaButton(useThisWidth,buttonName,useThisHeight,buttonSpaces,_debug,-1));
                                                    
                                                        _layoutObjectStack.addElement(OverflowButton);
                                                    }
                                                }
                                                else
                                                {
                                                    //We add the button like normal:
                                                    OverflowButton = new Hashtable();
                                                    OverflowButton.put("Name",buttonName);
                                                    fontWidth = ((int) ((useThisWidth / spaceWidth) + 0.5F));;
                                                    buttonSpaces = "";
                                                    while(fontWidth >= 0)
                                                    {
                                                        buttonSpaces += spaceChar;
                                                        fontWidth -= 1;
                                                    }
                                                    OverflowButton.put("Overlay",new YattaButton(useThisWidth,buttonName,useThisHeight,buttonSpaces,_debug,-1));
                                                            
                                                    _layoutObjectStack.addElement(OverflowButton);
                                                }
                                                
                                                //Now we update our information accordingly:
                                                oldButtonName = buttonName;
                                                currentX = dPx.doubleValue() + useThisWidth + 1;
                                                currentY = dPy.doubleValue();
                                            }
                                            
                                            bmp = null;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        
        public void DisplayGraphicGuiObjects(Graphics graphics)
        {
            Vector appGraphics = _islandApplicationManager.ApplicationEngine.GetGraphicObjects();
                                            
                                             if(_debug)
                                            {
                                                System.out.println("[INFO] Drawing a Vector Graphic");
                                            }
                                            // enumerate object container (GuiGraphicDetails)
                                                for(Enumeration e = appGraphics.elements(); e.hasMoreElements();)
                                                {
                                                    // paint graphics
                                                    islandSoftwareEngine.GraphicGuiDetails appGraphicDetails = (islandSoftwareEngine.GraphicGuiDetails) e.nextElement();
                                                    
                                                    if(appGraphicDetails !=null)
                                                    {
                                                        // Graphic conversion values
                                                        Double dPx = new Double(appGraphicDetails.PlacementX());
                                                        Double dPy = new Double(appGraphicDetails.PlacementY());
                                                        Double dLx = new Double(appGraphicDetails.LocationX());
                                                        Double dLy = new Double(appGraphicDetails.LocationY());
                                                        
                                                        if(appGraphicDetails.GraphicsObject instanceof Bitmap)
                                                        {
                                                            Bitmap bmp = (Bitmap) appGraphicDetails.GraphicsObject;
                                                            
                                                            if(_debug)
                                                            {
                                                                System.out.println("[INFO] this is the current details name:"+appGraphicDetails.Name);
                                                                
                                                                if(appGraphicDetails.Location!=null)
                                                                {
                                                                    System.out.println("[INFO] this is the current details locationX&locationY:"+appGraphicDetails.LocationX()+"&"+appGraphicDetails.LocationY());
                                                                }
                                                                
                                                                if(appGraphicDetails.Placement!=null)
                                                                {
                                                                    System.out.println("[INFO] this is the current details placementX&placementY:"+appGraphicDetails.PlacementX()+"&"+appGraphicDetails.PlacementY());
                                                                    
                                                                }
                                                            }
                                                            
                                                            // Background Background_Image
                                                            if(appGraphicDetails.Name.equals("Background_Image"))
                                                            {   
                                                                if((appGraphicDetails.Location!=null)&&(appGraphicDetails.Placement!=null))
                                                                {
                                                                    if(appGraphicDetails.Event == null)
                                                                    {
                                                                        appGraphicDetails.Event = "";
                                                                    }
                                                                        
                                                                    if(!appGraphicDetails.Event.equals("Removal"))
                                                                    {
                                                                        
                                                                        graphics.drawBitmap(dPx.intValue(),dPy.intValue(),bmp.getWidth(),bmp.getHeight(),bmp, dLx.intValue(),dLy.intValue());
                                                                    
                                                                        if(appGraphicDetails.HasText)
                                                                        {
                                                                            if(_debug)
                                                                            {
                                                                                System.out.println("[INFO] Graphic has text!");
                                                                            }
                                                                            //graphics.setColor(appGraphicDetails.ObjectColor);
                                                                            
                                                                            graphics.drawText(appGraphicDetails.ObjectText, dPx.intValue(),dPy.intValue());
                                                                        }
                                                                    }
                                                                    
                                                                    if(_componentsManager !=null)
                                                                    {
                                                                        //_componentsManager.invalidate();
                                                                    }
                                                                }
                                                            }
                                                            else
                                                            {
                                                                //! "Background_Image"
                                                                {   
                                                                    if((appGraphicDetails.Location!=null)&&(appGraphicDetails.Placement!=null))
                                                                    {
                                                                        if(appGraphicDetails.Event == null)
                                                                        {
                                                                            appGraphicDetails.Event = "";
                                                                        }
                                                                        
                                                                        if(!appGraphicDetails.Event.equals("Removal"))
                                                                        {
                                                                            
                                                                            graphics.drawBitmap(dPx.intValue(),dPy.intValue(),bmp.getWidth(),bmp.getHeight(),bmp, dLx.intValue(),dLy.intValue());
                                                                        
                                                                            if(appGraphicDetails.HasText)
                                                                            {
                                                                                if(_debug)
                                                                                {
                                                                                    System.out.println("[INFO] Graphic Has Text!!");
                                                                                }
                                                                                //graphics.setColor(appGraphicDetails.ObjectColor);
                                                                                
                                                                                graphics.drawText(appGraphicDetails.ObjectText, dPx.intValue(),dPy.intValue());
                                                                            }
                                                                        }
                                                                        
                                                                        if(_componentsManager !=null)
                                                                        {
                                                                            //_componentsManager.invalidate();
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
        }
        
        /**
         * Creates this screen's menu, which consists of the default main
         * screen menu as well as added items.
         * 
         * @see net.rim.device.api.ui.Screen#makeMenu(Menu,int)
         */
        protected void makeMenu( Menu menu, int instance ) 
        {
            super.makeMenu( menu, instance );
            
            //menu.add( _getServicesList );
            //menu.add( _loadSubscriptions );
            //menu.add( _loadProfile );
            
            
            if((IslandSoftwareState != islandSoftwareEngine.APP_ENGINE_STARTED))
            {
                menu.add( _getNewApps );
            }
        
            if((IslandSoftwareState == islandSoftwareEngine.APP_ENGINE_STARTED)&&(!_islandApplicationManager.preferencesLoaded))
            {
                if(this._islandApplicationManager!=null)
                {
                    if(!_islandApplicationManager.currentGrid.equals(""))
                    {
                        islandSoftwareEngine.GridScreenDetails appGridDetails = (islandSoftwareEngine.GridScreenDetails) _islandApplicationManager.ApplicationEngine.GetDisplayGridFromName(_islandApplicationManager.currentGrid);
                        int ord = 1010;
                        int prio = 10;
                    
                        for(Enumeration e = appGridDetails.GetGridScreens().elements(); e.hasMoreElements();)
                        {
                            String screenName = (String) e.nextElement();
                            
                            _getAppPreferences = new ApplicationPreferenceItem(screenName,++ord,++prio);
                            
                            menu.add(_getAppPreferences);
                        }
                        
                        //_islandApplicationManager.preferencesLoaded = true;
                    }
                }
            }
            
            //menu.add( _shareResources );
            
            //menu.add( _startLoadedApp );
            
        }
        
        // This is the method for home Island graphic elements
        public void paint(net.rim.device.api.ui.Graphics graphics)
        {
            if(_debug)
            {
                //System.out.println("[INFO] painting from Screen");
            }
            
            /*
            graphics.setColor(Color.RED);
            graphics.drawRect(100,100,100,50);
            graphics.drawText("Yoy1",250, 250);   
            */
            
            super.paint(graphics);
            
            /*
            graphics.setColor(Color.BLUE);
            graphics.drawRect(50,50,100,50);
            graphics.drawText("Yoy2",150, 150);                      
            */
        }            
        
        /**
        * Method to display a message to the user.
        * @param msg The message to display.
        */
        private void updateDisplay(final String msg, final int displayType)
        {
            invokeLater(new Runnable() 
            {
                public void run()
                {
                    //invalidate();
                    
                    if(msg!=null)
                    {
                        
                        _message.append(msg);
                        _message.append('\n');
                        
                    }
                    
                    switch(displayType)
                    {
                        case DISPLAY_IN_TEXT:
                            _rtf.setText(_message.toString());
                            break;
                        case DISPLAY_IN_DIALOG_TEXT: 
                            Dialog.alert(_message.toString());
                            break;
                        case DISPLAY_IN_ASK_DIALOG_TEXT:
                            Dialog.ask(Dialog.D_YES_NO,_message.toString());
                            break;
                        case DISPLAY_IN_DIALOG_FORM_TEXT:
                            _rtf.setText(_message.toString());
                            break;
                        case DISPLAY_GRAPHIC_OBJECTS:
                            try
                            {
                                if(_graphicsManager != null)
                                {
                                    _graphicsManager.invalidate();
                                }
                                else
                                {
                                    System.out.println("[INFO] _graphicsManagerUpdate is null");
                                }
                            }
                            catch(Exception e)
                            {
                                System.out.println("[ERROR] problems replacing graphic manager:"+e.toString());
                            }
                            break;
                        case DISPLAY_GUI_COMPONENTS:
                            try
                            {
                                if(_componentsManager != null)
                                {
                                    _componentsManager.invalidate();
                                }
                                else
                                {
                                    System.out.println("[INFO] _componentsManagerUpdate is null");
                                }
                            }
                            catch(Exception ex)
                            {
                                System.out.println("[ERROR] problems replacing component manager:"+ex.toString());
                            }
                            break;
                       case DISPLAY_GRID_OVERLAYS:
                            try
                            {
                                if(_overFlow != null)
                                {
                                    _loadedLabels = false;
                                    _overFlow.invalidate();
                                }
                                else
                                {
                                    System.out.println("[INFO] _overFlowUpdate is null");
                                }
                            }
                            catch(Exception ex)
                            {
                                System.out.println("[ERROR] problems replacing overFlow manager:"+ex.toString());
                            }
                            break;
                    }
                }
            });
        }

        /**
        * Invoked when a key is pressed.
        * 
        * @see net.rim.device.api.ui.Screen#keyChar(char,int,int)
        */
        public boolean keyChar(char key, int status, int time) 
        {
            if(_debug)
            {
                System.out.println("[INFO] inside the keyChar something was clicked!");
            }
            
            switch (key) 
            {
                default:
                    try
                    {
                        if(IslandSoftwareState == islandSoftwareEngine.APP_ENGINE_STARTED)
                        {
                            if(_islandApplicationManager!=null)
                            {
                                if(_islandApplicationManager.ApplicationEngine.PerformOperationForEvent(islandSoftwareEngine.USER_DEVICE_KEY_EVENT, key))
                                {
                                    // delete components from manager
                                    for(int a = 0; a<_componentObjectStack.size(); a++)
                                    {
                                        _componentsManager.delete((Field)_componentObjectStack.elementAt(a));
                                    }
                                    
                                    // dump components stack
                                    _componentObjectStack.empty();
                                    
                                    /* same for graphics */
                                }
                                
                                if(_debug)
                                {
                                    System.out.println("[AppEngine-INFO] key clicked");
                                }
                            }
                            
                            if(_debug)
                            {
                                //System.out.println("[INFO] this is the key entered:"+key);
                            }
                        }
                        
                        updateDisplay("",DISPLAY_GRAPHIC_OBJECTS);
                        
                        updateDisplay("",DISPLAY_GUI_COMPONENTS);
                
                        updateDisplay("",DISPLAY_GRID_OVERLAYS);
                    }
                    catch(Exception ex)
                    {
                        if(_debug)
                        {
                            System.out.println("[ERROR] problems performing operation for key event:"+ex.toString());
                        }
                    }
                    
                    break;
            }
    
            return super.keyChar(key, status, time);
        }        

        public boolean navigationClick( int status, int time ) 
        {
            Field fieldWithFocus = null;
            
            if(_debug)
            {
                System.out.println("[INFO] navigation Clicked!");
            }
            
            
            if(this._islandResourceManager.GetUserDisplayDefault().equals("Gui"))
            {
                fieldWithFocus = _componentsManager.getFieldWithFocus();
            }
            
            if(this._islandResourceManager.GetUserDisplayDefault().equals("Grid"))
            {
                fieldWithFocus = _overFlow.getFieldWithFocus();
            }
            
            if(fieldWithFocus!=null)
            {
                if(fieldWithFocus instanceof ButtonField)
                {
                    try
                    {
                        // Set necessary flags for the app we're about to load
                        if(IslandResourceState == islandUserResource.USER_APPLICATIONS)
                        {
                            ButtonField bf = (ButtonField) fieldWithFocus;
                                        
                            // Set DOWNLOAD
                            DOWNLOAD = this._islandResourceManager.GetApplicationResourceLocation(bf.getLabel());
                                    
                            if(_debug)
                            {
                                System.out.println("[INFO] button clicked was:" + bf.getLabel());
                                System.out.println("[INFO] DOWNLOAD Loc is:" + DOWNLOAD);
                            }
                                                                            
                            // Set LOADED_APP
                            LOADED_APP = bf.getLabel();
                                    
            
                            if(this._islandResourceManager.GetUserDisplayDefault().equals("Gui"))
                            {
                                // delete components from manager
                                for(int a = 0; a<_componentObjectStack.size(); a++)
                                {
                                    Field f = (Field)_componentObjectStack.elementAt(a);
                                    if(f instanceof ButtonField)
                                    {
                                        _componentsManager.delete((Field)_componentObjectStack.elementAt(a));
                                    }
                                    
                                    if(f instanceof SeparatorField)
                                    {
                                        _componentsManager.delete((Field)_componentObjectStack.elementAt(a));
                                    }
                                    
                                    if(f instanceof YattaButton)
                                    {
                                        _componentsManager.delete((Field)_componentObjectStack.elementAt(a));
                                    }
                                }
                                        
                                // dump components stack
                                _componentObjectStack.empty();
                            }        
                            
                            if(this._islandResourceManager.GetUserDisplayDefault().equals("Grid"))
                            {
                                // delete overflow from manager
                                for(int a = 0; a<_overFlowObjectStack.size(); a++)
                                {
                                    Field f = (Field)_overFlowObjectStack.elementAt(a);
                                    if(f instanceof ButtonField)
                                    {
                                        _overFlow.delete((Field)_overFlowObjectStack.elementAt(a));
                                    }
                                    
                                    if(f instanceof SeparatorField)
                                    {
                                        _overFlow.delete((Field)_overFlowObjectStack.elementAt(a));
                                    }
                                }
                                        
                                // dump oveflow stack
                                _overFlowObjectStack.empty();
                            }  
                            
                            
                            
                            // set Default SoftwareEngine state
                            if(!_applicationIsRunning)
                            {
                                // start it
                                _applicationIsRunning = true;
                                _islandApplicationManager = new IslandApplicationThread();
                                _islandApplicationManager.start();
                                        
                                if(_debug)
                                {
                                    System.out.println("[INFO] starting selected application");
                                }
                            }
                        }
                        
                       
                    }
                    catch(Exception ex)
                    {
                        if(_debug)
                        {
                            System.out.println("[ERROR] problems fulfilling button action:"+ex.toString());
                        }
                    }
                } // Button Click Handled
                else if(fieldWithFocus instanceof YattaButton)
                {
                    try
                    {
                        YattaButton yButt = (YattaButton) fieldWithFocus;
                        String buttonLabel = yButt.getButtonName();
                        
                        //_rtf.setText(yButt.getButtonName());
                        _gridBox.setText(buttonLabel);
                        
                        if(_debug)
                        {
                            System.out.println("[BUTTON PRINT] - The Yatta Button clicked was: " + buttonLabel);
                        }
                    }
                    catch(Exception e)
                    {
                        if(_debug)
                        {
                            System.out.println("[ERROR] problem fulfilling Yatta button action:"+e.toString());
                        }
                    }
                }    
                else if(fieldWithFocus instanceof RichTextField)
                {
                    try
                    {
                        RichTextField rtf = (RichTextField) fieldWithFocus;
                        String buttonLabel = rtf.getLabel();
                        
                        if(_debug)
                        {
                            System.out.println("[BUTTON PRINT] - The RichTextField clicked was: " + buttonLabel);
                        }
                        
                        RichTextField topBox = null;
                        int boxSearch = 0;
                        
                        while(boxSearch < _graphicsManager.getFieldCount())
                        {
                            if(_graphicsManager.getField(boxSearch) instanceof RichTextField)
                            {
                                topBox = (RichTextField) _graphicsManager.getField(boxSearch);
                                if(topBox.getLabel() != "Textbox")
                                    topBox = null;
                                else
                                    break;
                            }
                            boxSearch += 1;
                        }
                        
                        if(topBox != null)
                            topBox.setText(buttonLabel);
                        
                        
                        /*
                        if((IslandSoftwareState != islandSoftwareEngine.APP_ENGINE_STOPPED)&&
                        (_applicationIsRunning))
                        {
                            
                            if(_debug)
                            {
                                System.out.println("[INFO] island App is not null");
                            }
                    
                            if(_islandApplicationManager!=null)
                            {
                                if(_debug)
                                {
                                    System.out.println("[AppEngine-INFO] navigation clicked");
                                }
                
                                int likelyLocation = 0;
                                if(rtf.getIndex() == 0)
                                {
                                    likelyLocation = rtf.getHeight();
                                }
                                else
                                {
                                    likelyLocation = rtf.getIndex() * rtf.getHeight(); 
                                }
                                
                                
                                for(int i=0; i< _graphicsManager.getFieldCount();i++)
                                {
                                    try
                                    {
                                        // Get rid of the separators
                                        if(_graphicsManager.getField(i) instanceof SeparatorField)
                                        {
                                            SeparatorField b_sf = (SeparatorField) _graphicsManager.getField(i);
                                            _graphicsManager.delete(b_sf);
                                            _componentObjectStack.removeElement(b_sf);
                                        }
                                                    
                                        if(_graphicsManager.getField(i) instanceof RichTextField)
                                        {
                                            // for DEMO/PROTO remove blanks
                                            RichTextField b_rtf = (RichTextField) _graphicsManager.getField(i);
                                            if(b_rtf.getText().equals(""))
                                            {
                                                _graphicsManager.delete(b_rtf);
                                                _componentObjectStack.removeElement(b_rtf);
                                            }
                                        }
                                    }
                                    catch(Exception ec)
                                    {
                                        if(_debug)
                                        {
                                            System.out.println("[ERROR] problems initializing component Screen:"+ec.toString());
                                        }
                                    }
                                }
                 
                                if(_islandApplicationManager.ApplicationEngine.PerformOperationForEvent(rtf,islandSoftwareEngine.USER_DEVICE_SELECTION_EVENT,likelyLocation))
                                {
                                    if(_debug)
                                    {
                                        //System.out.println("[INFO] User device selection fullfilled for index:"+rtf.getIndex());
                                    }
                                    
                                    // set software state
                                    IslandSoftwareState = _islandApplicationManager.ApplicationEngine.GetEngineState();
                                    
                                    if(IslandSoftwareState == 0)
                                    {
                                        IslandSoftwareState = islandSoftwareEngine.APP_OPERATION_COMPLETED;
                                    }
                                }
                                
                                // Update Graphics
                                updateDisplay("",DisplayGraphicObjects);
                                
                                // Update Components            
                                updateDisplay("",DisplayGuiComponents);
                            }
                        }*/
                    }
                    catch(Exception e)
                    {
                        if(_debug)
                        {
                            System.out.println("[ERROR] problem fulfilling button action:"+e.toString());
                        }
                    }
                }
            } // Field With Focus !=null

            // Once we consume the click we are done
            return true;
        }                

        


        /**
        * @see net.rim.device.api.ui.component.DialogClosedListener#dialogClosed(Dialog,int)
        */
        public void dialogClosed( Dialog dialog, int choice ) 
        {
            if( dialog == _d && choice == Dialog.CANCEL ) 
            {
                /*
                // Tell the setup thread that the user on this end has cancelled the request.
                synchronized( _twoPlayerSession ) 
                {
                    _twoPlayerSession.notify();
                }
                */
            }
        }
        
        /**
         * @see net.rim.device.api.ui.Screen#close()
        */
        public void close() 
        {
            this._islandApplicationManager.ApplicationEngine.stopEngine();
            
            IslandSoftwareState = this._islandApplicationManager.ApplicationEngine.GetEngineState();
            
            super.close();
        }
        
        private Object getLockObject()
        {
            return this;
        }

        /**
        * A private inner class to manage local thread.
        * Not static since we access some methods in our parent.
        * Used for local file management of homeIsland related 
        * items. This thread takes requests from the user, island Links and 
        * applications to access subscriptions, profile & resources.
        * It manages local data settings & security for user island;
        * knows how to travel island to island and knows how to get resources
        * for users, services and applications.
        */
        private class HomeIslandThread extends Thread
        {
            // Class flags
            private boolean debug;
            private boolean userAppsLoaded;
            private boolean userSubsLoaded;
            private boolean userServsLoaded;
            
            // Session compression
            private int compressionMethod = islandSubscriptionResource.GZIP;
            
            // Save resources locally or point to remote loc
            private islandResourceManager rMan;
            private islandSubscriptionResource subscriptionItems;
            private islandApplicationResource applicationItems;
            private islandUserResource userItems;
            
            // Keys
            private String pubKey;
            private String priKey;
            
            // readers/writers for local data
            private InputStreamReader _read;
            private OutputStreamWriter _write;
            
            public HomeIslandThread()
            {
                this.debug = true;
                this.userAppsLoaded = false;
                this.userSubsLoaded = false;
                this.userServsLoaded = false;
            }
        
            public void SetDebug(boolean d)
            {
                this.debug = d;
            }
            
            public String GetApplicationResourceLocation(String appName)
            {
                String location = "";
                
                try
                {
                    if(userItems !=null)
                    {
                        location = userItems.GetResourceLocationFor(appName);
                    }
                }
                catch(Exception e)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems getting app resource location:"+e.toString());
                    }
                }
                
                return location;
            }
            
            public String GetCurrentApplicationUserName()
            {
                
                return "";
            }
            
            
            public String GetCurrentApplicationPassWord()
            {
                
               return ""; 
            }
            
            public String GetUserDisplayDefault()
            {
                String displayStyle = "Gui";
                
                if(this.userItems!=null)
                {
                    displayStyle = userItems.GetDisplayFormat();
                }
                else
                {
                    if(getCurrentUserInfo())
                    {
                        displayStyle = userItems.GetDisplayFormat();
                    }
                }
                
                return displayStyle;
            }
            
            public void DownloadAppEnumeration(Enumeration aire)
            {
                //Enumeration aire = this.applicationItems.getResourceEnumeration();
            
                try
                {
                    // Enumerate and get resource
                    Hashtable hTable;
            
                    for (Enumeration e = aire; e.hasMoreElements();) 
                    {
                        Object element = e.nextElement();
            
                        if(element instanceof String)
                        {
            
                            // Set resource loc 
                            if(element.toString().indexOf("http:") > -1)
                            {
                                DOWNLOAD = (String)element;
            
                                // replace resource loc with resource Value
                                this.getApplicationResource();
            
                                updateDisplay("[INFO] downloaded Needed resource File",DISPLAY_IN_TEXT);                                   
                            
                                if(this.debug)
                                {
                                    System.out.println(element);
                                }
                            }
                        }
                        else if(element instanceof Hashtable)                        
                        {
                            // Set resource loc 
                            hTable = (Hashtable) element; 
                            
                            DOWNLOAD = (String) hTable.get("URL");
            
                            if(DOWNLOAD !=null)
                            {
                                if(this.debug)
                                {
                                    System.out.println("about to get:"+DOWNLOAD);
                                }
                                
                                if(DOWNLOAD != null)
                                {
                                    if(DOWNLOAD.length()>0)
                                    {
                                        // replace resource loc with resource Value
                                        this.getApplicationResource();
                        
                                        //updateDisplay("[INFO] downloaded Needed resource Files",DISPLAY_IN_TEXT);                                   
                                    }
                                }
                            }
                            else
                            {
                                for (Enumeration innerE = hTable.elements(); innerE.hasMoreElements();) 
                                {
            
                                    Object objTable = innerE.nextElement();
            
                                    if(objTable instanceof Hashtable)
                                    {
                                        Hashtable thisTable = (Hashtable) objTable;
                                        DOWNLOAD = (String) thisTable.get("URL");
            
                                        if(this.debug)
                                        {
                                            System.out.println("DownloadAppEnumeration Method - about to get:"+DOWNLOAD);
                                        }
            
                                        if(DOWNLOAD != null)
                                        {
                                            if(DOWNLOAD.length()>0)
                                            {
                                                // replace resource loc with resource Value
                                                this.getApplicationResource();
            
                                                //updateDisplay("[INFO] downloaded Needed resource Files",DISPLAY_IN_TEXT);                                   
                                            }
                                        }
                                    }
                                }   
                            }
                        }
                    }
                    
                    updateDisplay("[INFO] Finished downloading application Files",DISPLAY_IN_TEXT);
            
                }
                catch(Exception ex)
                {
                    if(this.debug)
                    {
            
                        System.out.println("[ERROR] problems getting application Resource:"+ex.toString());
                    }
                }
            }
            
            
            /**
             * Function throws up a dialog loking for 
             * the operators username at the least. Basically 
             * a handle to go get/load/or create the users profile.
             * We could possible also ask for the username and
             * locations for profile.
             */
            private boolean getCurrentUserInfo()
            {
                try
                {
                    // loaded from the global hash
                    if(loadProfile())
                    {
                        _profileLoaded = true;
                    }
                    
                    // put Available Apps in the global Hash
                    if(getAvailableApplicationsForCurrentUser())
                    {
                        // load available apps in User Resources
                        userAppsLoaded = true;
                    }
                    
                    // put Available Subs in global Hash
                    if(getAvailableSubscriptionsForCurrentUser())
                    {
                        // load available subs in User Resources
                        this.userSubsLoaded = true;
                    }
                        
                    // put Available services in global Hash
                    if(getAvailableServicesForCurrentUser())
                    {
                        // load available resource services for current user
                        this.userServsLoaded = true;
                    }
                }
                catch(Exception e)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems getting current user information:"+e.toString());
                    }                        
                }
                           
                if(_profileLoaded )
                {
                    return true;
                }           
                else
                {
                    return false;
                }        
            }
            
            /**
             * Get the userDetails and definately the profile.
             * Responsible for setting the Loaded flags.
             */
            private void getDetailsForCurrentUser()
            {
                // Load everything into user Resource
                if(getCurrentUserInfo())
                {
                    _userDetailsLoaded = true;
                }
            }
            
            /**
             * Function, using the details from the current user
             * loaded gets the applications listed in the profile.
             */
            private boolean getAvailableApplicationsForCurrentUser()
            {
                boolean applicationsAvailable = false;
                
                try
                {
                    // See if user has applications now
                    if(userDetails !=null)
                    {
                        if(userDetails.size()>0)
                        {
                            if(userDetails.containsKey("AvailableApplications"))
                            {
                                Hashtable apps = (Hashtable) userDetails.get("AvailableApplications");
                                
                                if(apps !=null)
                                {
                                    if(apps.size()>0)
                                    {
                                        if(loadAvailableApplicationsForCurrentUser())
                                        {
                                            applicationsAvailable = true;
                                        }
                                    }
                                    else
                                    {
                                        // try getting apps from local source
                                        
                                        // try getting apps from Looping Service Subscriptions
                                    }
                                }
                                else
                                {
                                    // try getting data from local source
                                    
                                    // try getting applications from Looping Service Subscriptions
                                }
                            }
                            else
                            {
                                // try getting data from local source
                                    
                                    // try getting applications from Looping Service Subscriptions
                            }
                        }
                    }
                    
                }
                catch(Exception ex)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems getting user available applications:"+ex.toString());
                    }                        
                }
                
                return applicationsAvailable;
            }

            private boolean loadAvailableApplicationsForCurrentUser()
            {
                boolean loaded = false;
                
                try
                {
                    // See if sevice has applications
                    if(userDetails !=null)
                    {
                        if(userDetails.size()>0)
                        {
                            if(userDetails.containsKey("AvailableApplications"))
                            {
                                Hashtable apps = (Hashtable) userDetails.get("AvailableApplications");
                                
                                if(apps !=null)
                                {
                                    if(apps.size()>0)
                                    {
                                        if(userItems == null)
                                        {
                                            if(rMan == null)
                                            {
                                                rMan = new islandResourceManager();
                                            }
                                            
                                            userItems = (islandUserResource)rMan.GetResourceFor(islandResource.USER);
                                        }
                                        
                                        this.userItems.SetUserApps(apps);
                                    
                                        loaded = true;
                                    }
                                    else
                                    {
                                        // get applications from HomeIslandLocation
                                    }
                                }
                                else
                                {
                                    // get applications from HomeIslandLocation
                                }
                            }
                        }
                    }
                }
                catch(Exception ex)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems getting user available applications:"+ex.toString());
                    }                        
                }
                
                return loaded;
            }

            private boolean getAvailableSubscriptionsForCurrentUser()
            {
                return true;
            }
            
            
            private boolean getAvailableServicesForCurrentUser()
            {
                return true;
                
            }

            /**
            * <description>
            */
            private boolean saveProfile()
            {
                boolean profileSaved = false;
                
                return profileSaved;
            }
            
            /**
            * This function loads the bare bones 
            * profile information from global memory.
            * If that is possible it returns true, if not
            * it returns false.
            */
            private boolean loadProfile()
            {
                boolean profileLoaded = false;
            
                try
                {
                    if(userDetails!=null)
                    {
                        if(userDetails.containsKey("ProfileSettings"))
                        {
                            Hashtable ps = (Hashtable) userDetails.get("ProfileSettings");
                            
                            if(this.userItems == null)
                            {
                                if(rMan == null)
                                {
                                    rMan = new islandResourceManager();
                                }
                                  
                                this.userItems = (islandUserResource)rMan.GetResourceFor(islandResource.USER);
                            }
                            
                            this.userItems.SetUserProfile(ps);
                            
                            if(userItems.HaveValidProfile())
                            {
                                profileLoaded = true;
                            }
                        }
                    }
                }
                catch(Exception e)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems loading current user profile:"+e.toString());
                    }
                }
                
                return profileLoaded;
            }
            
            /**
             * display Profile gui.
             */
            private void showProfile()
            {
                if(this.debug)
                {
                    //System.out.println("[INFO] showing Profile Gui");
                }
                
            }
            
            /**
             * display user info gui form
             * @return <description>
             */
            private void showUserInfo()
            {
                if(this.debug)
                {
                    //System.out.println("[INFO] showing User Datails Gui");
                }
            }                
            
            /**
             * Function loops through Available Applications and creates
             * a Button (or later an icon) for the application 
             */
            private void showUserApplications()
            {
                synchronized(UiApplication.getEventLock())
                {
                    try
                    {
                        _rtf.setText("         Available Applications");
                        
                        SeparatorField sf = new SeparatorField();
                        
                        if(this.userItems.GetDisplayFormat().equals("Gui"))
                        {
                            _componentObjectStack.addElement(sf);
                            _componentsManager.add(sf);
                            
                            // loop through apps list and add buttons w/ 1st chr
                            for(Enumeration e = this.userItems.GetLoadedApplications().keys(); e.hasMoreElements();)
                            {
                                String k = (String) e.nextElement();
                                ButtonField bf = new ButtonField(k,FIELD_HCENTER);
                                
                                _componentsManager.add(bf);
                                _componentObjectStack.addElement(bf);
                                
                                sf = new SeparatorField();
                                _componentsManager.add(sf);
                                _componentObjectStack.addElement(sf);
                            }
                        }
                        
                        if(this.userItems.GetDisplayFormat().equals("Grid"))
                        {
                            _overFlowObjectStack.addElement(sf);
                            _overFlow.add(sf);
                            
                            // loop through apps list and add buttons w/ 1st chr
                            for(Enumeration e = this.userItems.GetLoadedApplications().keys(); e.hasMoreElements();)
                            {
                                String k = (String) e.nextElement();
                                ButtonField bf = new ButtonField(k,FIELD_HCENTER);
                                
                                _overFlow.add(bf);
                                _overFlowObjectStack.addElement(bf);
                                
                                sf = new SeparatorField();
                                _overFlow.add(sf);
                                _overFlowObjectStack.addElement(sf);
                            }
                        }
                        
                        //_homeIslandIsRunning = true;
                    }
                    catch(Exception e)
                    {
                        System.out.println("[ERROR] problems showing available Applcations:"+e.toString());
                    }
                }
            }
            
            private void showUserSubscriptions()
            {
                
            }
            
            private void showUserServices()
            {
                
            }
            
            
            
            /**
             * Looks up Service details to get Applications, 
             * Subscriptions, Resources and other minutia.
             */
            private void getSubscriptionDetails()
            {
                
            }
            
            /**
            * <description>
            */
            private boolean saveSubscription()
            {
                boolean subscriptionSaved = false;
                
                return subscriptionSaved;
            }
            
            /**
            * Gets the subscription details from the provider,
            * saves resources and available applications if 
            * necessary. After the first load the subscription can
            * be saved if directed in the details.
            */
            private boolean loadSubscription()
            {
                boolean subscriptionLoaded = false;
                
                return subscriptionLoaded;
            }
            
            private boolean saveSubscriptionResource()
            {
                boolean resourceSaved = false;
                
                return resourceSaved;
            }
            
            private islandSubscriptionResource getSubscriptionResource()
            {
                
                return subscriptionItems;
            }
            
            /**
             * Function loads all available apps for a given service.
             * The loaded Service is set in a static variable.
             * @return <description>
             */
            private boolean getAvailableApplicationsForSubscription()
            {
                boolean gotAvailableApplications = false;
                
                try
                {
                
                    // If theis service has applications
                    if(serviceDetails.size()>0)
                    {
                        if(serviceDetails.containsKey("HasApplications"))
                        {
                            // Seeif it is true
                            boolean hasApps = (boolean) ((Boolean)serviceDetails.get("HasApplications")).booleanValue();
                            
                            if(hasApps)
                            {
                                // Load then Find my Loaded App
                                if(serviceCommands.size()>0)
                                {
                                    // lookup command to get Applications
                                    if(serviceCommands.containsKey("GetAvailableApplications"))
                                    {
                                       
                                        String getAppsCommand = (String)serviceCommands.get("GetAvailableApplications");
                            
                                        UserMessage = getAppsCommand;
                                    
                                        IslandTravelState = islandTravelAgent.WAIT_FOR_OUTPUT;
                                        
                                        while(IslandTravelState == islandTravelAgent.WAIT_FOR_OUTPUT)
                                        {
                                            if(!_islandLinksIsRunning)
                                            {
                                                _islandConnectionManager = new IslandLinksConnectThread();
                                                _islandConnectionManager.start();
                                            }
                                            
                                            // Wait for the travel thread to send command
                                            getLockObject().wait(180000);
                                            
                                            if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                            {
                                                // load the user data into the right Hashtable
                                                loadAvailableApplicationsForSubscription();
                                                
                                                // Done
                                                IslandTravelState = islandTravelAgent.DONE;
                                                
                                                if(availableApplications.size()>0)
                                                {
                                                    gotAvailableApplications = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        getSubscriptionDetails();
                        
                        if(availableApplications.size()>0)
                        {
                            gotAvailableApplications = true;
                        }
                    }
                }
                catch(Exception Ex)
                {
                    updateDisplay("[ERROR] problems getting Available Applications",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(Ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems getting Available Applications:"+Ex.toString());
                    }
                }
                
                return gotAvailableApplications;
            }
    
            /**
             * Function takes the data result from the yatta 
             * query/command and loads the current available
             * application Hashtable in memory here, then saves it
             * to disk somewheres. 
             * @return <description>
             */
            private boolean loadAvailableApplicationsForSubscription()
            {
                boolean applicationsLoaded = false;
                
                int availableApplicationsIndx = 0;
                int mappingKeyIndx = 0;
                int mappingValueIndx = 0;
                int mappingKey = 0;
                String mappingValue = "";
                   
                try
                {         
                            /* load the points we just got 
                            while(translationPointIndx < UserMessage.length())
                            {
                                // Load em up in the applicationTranslationMapping
                                mappingKeyIndx = UserMessage.indexOf("=",translationPointIndx);
                                mappingValueIndx = UserMessage.indexOf(";",mappingKeyIndx);
                                
                                mappingKey = UserMessage.substring(translationPointIndx,mappingKeyIndx);
                                mappingValue = UserMessage.substring(mappingKeyIndx, mappingValueIndx);
                                
                                translationPointIndx = mappingValueIndx;
                                
                                applicationTranslationMapping.add(new Integer(mappingKey).intValue(), mappingValue);
                            }
                            */
                            
                    applicationsLoaded = true;
                }
                catch(Exception ex)
                {
                    updateDisplay("[ERROR] problems loading Available Applications",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems loading Available Applications:"+ex.toString());
                    }
                }
                
                return applicationsLoaded;
            }
            
            /**
            * <description>
            */
            private boolean saveApplication()
            {
                boolean applicationSaved = false;
                
                return applicationSaved;
            }
            
            /**
            * Function to load an application that will go 
            * somewhere on the internet and get the application
            * details, and if neccessary Save them locally.
            * Icons, Resources, translator mappings are the 
            * most likely. Usually details on yatta, in a database,
            * or xml file via http. Assumes LOADED_APP is already set.
            */
            private boolean loadApplication()
            {
                boolean applicationLoaded = false;
                
                try
                {
                    // look for the details in the resource objects
                    applicationItems = getApplicationResource();
                    
                    // if it's not there go back to the island links
                    if(applicationItems == null)
                    {
                        if(serviceCommands.size()>0)
                        {
                            // get the details
                            if(serviceCommands.containsKey("GetApplicationDetails"))
                            {
                                        
                                String getAppDetailsCommand = (String)serviceCommands.get("GetApplicationDetails");
                                
                                UserMessage = getAppDetailsCommand;
                                        
                                IslandTravelState = islandTravelAgent.WAIT_FOR_OUTPUT;
                                            
                                while(IslandTravelState == islandTravelAgent.WAIT_FOR_OUTPUT)
                                {
                                    if(!_islandLinksIsRunning)
                                    {
                                        _islandConnectionManager = new IslandLinksConnectThread();
                                        _islandConnectionManager.start();
                                    }
                                                
                                    // Wait for the travel thread to send command
                                    getLockObject().wait(180000);
                                                
                                    if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                    {
                                        // load em up in memory
                                    
                                        // Done
                                        IslandTravelState = islandTravelAgent.DONE;
                                                    
                                        if(applicationDetails.size()>0)
                                        {
                                            applicationLoaded = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    // save app if required
                    saveApplication();
                    
                    /*
                    // save app resources also if it's required
                    saveApplicationResource();
                    */
                    
                }
                catch(InterruptedException iex)
                {
                    updateDisplay("[ERROR] Interrupted Exception problems loading Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] Interrupted Exception problems loading Application:"+iex.toString());
                    }
                }
                catch(Exception ex)
                {
                    updateDisplay("[ERROR] problems loading Applications",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems loading Applications:"+ex.toString());
                    }
                }
                
                return applicationLoaded;
            }
            
            /**
             * Use resource manager to create the correct resource 
             * container and then add 
             * @param resourceType <description>
             * @return <description>
             */
            private boolean saveApplicationResource(int resourceType)
            {
                boolean resourceSaved = false;
                
                try
                {
                    if(rMan == null)
                    {
                        // Setting debug flag 
                        rMan = new islandResourceManager(true);
                    }
                    
                    if(this.applicationItems == null)
                    {
                        this.applicationItems = (islandApplicationResource) rMan.GetResourceFor(islandApplicationResource.APPLICATION);
                    }
                    
                    if(resourceType == islandApplicationResource.XML_GUI_RESOURCE_LIST)
                    {
                        this.applicationItems.addGuiResource(resourceType, UserDownload);
                    
                        // Update resourceValues
                        applicationResourceValues.add(new Integer(islandApplicationResource.FILE), this.applicationItems);
                    
                        // Remove existing Details
                        //applicationDetails.remove("ResourceList");
                    
                        // Add new list
                        applicationDetails.put("ResourceList", applicationResourceValues);
                    
                    }
                    
                    if(resourceType == islandApplicationResource.XML_MEDIA_RESOURCE_LIST)
                    {
                        this.applicationItems.addMediaResource(resourceType, UserDownload);
                    
                        // Update resourceValues
                        applicationResourceValues.add(new Integer(islandApplicationResource.FILE), this.applicationItems);
                    
                        // Remove existing Details
                        //applicationDetails.remove("ResourceList");
                    
                        // Add/Update new list
                        applicationDetails.put("ResourceList", applicationResourceValues);
                    
                    }
                    
                    if(resourceType == islandApplicationResource.XML_DATA_RESOURCE_LIST)
                    {
                        this.applicationItems.addDataResource(resourceType, UserDownload);
                    
                        // Update resourceValues
                        applicationResourceValues.add(new Integer(islandApplicationResource.FILE), this.applicationItems);
                    
                        // Remove existing Details
                        //applicationDetails.remove("ResourceList");
                    
                        // Add/Update new list
                        applicationDetails.put("ResourceList", applicationResourceValues);
                    
                    }
                    
                    if(resourceType == islandApplicationResource.XML_ADDRESS_LIST)
                    {
                        this.applicationItems.addAddress(resourceType, UserDownload);
                    
                        // Update resourceValues
                        applicationResourceValues.add(new Integer(islandApplicationResource.FILE), this.applicationItems);
                    
                        
                        // Add/Update new list
                        applicationDetails.put("AddressesList", applicationResourceValues);
                    
                    }
                    
                    if(resourceType == islandApplicationResource.XML_OPERATION_LIST)
                    {
                        this.applicationItems.addOperation(resourceType, UserDownload);
                    
                        // Update resourceValues
                        applicationResourceValues.add(new Integer(islandApplicationResource.FILE), this.applicationItems);
                    
                        // Add/Update new list
                        applicationDetails.put("OperationsList", applicationResourceValues);
                    
                    }
                    
                    if(resourceType == islandApplicationResource.XML_TRANSLATION_LIST)
                    {
                        this.applicationItems.addTranslation(resourceType, UserDownload);
                    
                        // Update resourceValues
                        applicationResourceValues.add(new Integer(islandApplicationResource.FILE), this.applicationItems);
                    
                        // Add/Update new list
                        applicationDetails.put("TranslationList", applicationResourceValues);
                    
                    }
                }
                catch(Exception ex)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems saving Resource:"+ex.toString());
                        ex.printStackTrace();
                    }
                }
                
                return resourceSaved;
            }
            
            private islandApplicationResource getApplicationResource()
            {
                try
                {
                    IslandTravelState = islandTravelAgent.GET;
                    UserDownload = "";
                    UserDataDownload = null;
                                
                    // Get xml file from server loc
                    while((IslandTravelState == islandTravelAgent.GET)||(UserDataDownload==null))
                    {
                        if(!_islandLinksIsRunning)
                        {
                            _islandLinksIsRunning = true;
                                            
                            _islandConnectionManager = new IslandLinksConnectThread();
                            _islandConnectionManager.start();
                            
                            IslandTravelState = islandTravelAgent.GET;
                        }
                                        
                        synchronized(getLockObject())
                        {
                            // Let IslandTravel Manager Know We Wanna download something else
                            getLockObject().notify();
                            
                            // Wait for a response from the IslandTravel Manager
                            getLockObject().wait(180000);
                        }
                    }
                    
                    // add to applicationItems
                    this.applicationItems.updateGuiResourceLocationWithValue(DOWNLOAD, UserDataDownload);
                    
                    // add to applicationItems
                    this.applicationItems.updateDataResourceLocationWithValue(DOWNLOAD, UserDataDownload);
                    
                    // add to applicationItems
                    this.applicationItems.updateMediaResourceLocationWithValue(DOWNLOAD, UserDataDownload);
                    
                    // update application Resources
                    applicationResourceValues.add(new Integer(islandApplicationResource.FILE),applicationItems);
                }
                catch(Exception e)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems getting Application Resource:"+e.toString());
                        e.printStackTrace();
                    }
                }
                
                return applicationItems;
            }
            
            /**
            * <description>
            */
            private String getPublicKey()
            {
                return pubKey;
            }
            
            /**
            * <description>
            */
            private boolean createPublicKey()
            {
                boolean pubKeyCreated = false;
                
                return pubKeyCreated;
            }
            
            /**
            * <description>
            */
            private String getPrivateKey()
            {
                
                return priKey;
            }
            
            /**
            * <description>
            */
            private boolean createPrivatekey()
            {
                boolean priKeyCreated = false;
                
                return priKeyCreated;
            }
            
            private void getDetailsForCurrentApplication()
            {
                try
                {
                    // Get available application location for Loaded App
                    if(availableApplications.size()>0)
                    {
                        // Look up this application
                        if(availableApplications.containsKey(LOADED_APP))
                        {
                            // Reset current app Details continer  
                            applicationDetails = (Hashtable) availableApplications.get(LOADED_APP);
                        }
                        else
                        {
                            // Use islandLinks to update the applications list
                            getAvailableApplicationsForSubscription();
                        
                            // Load the one you want more info about
                            loadApplication();
                        
                        }
                    }
                    else
                    {
                        // Use islandLinks to get the application
                        getAvailableApplicationsForSubscription();
                        
                        // Load the one you want more info about
                        loadApplication();
                    }
                }
                catch(ClassCastException ce)
                {
                    if(debug)
                    {
                        System.out.println("[ERROR] casting problems getting details for loaded Application:"+ce.toString());
                    }
                    
                    // Probably a listing with no app details
                    loadApplication();
                }
                catch(Exception ex)
                {
                    updateDisplay("[ERROR] problems getting details for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems getting details for loaded Application:"+ex.toString());
                    }
                    
                }
            }
            
            private void getTranslatorPointsForCurrentApplication()
            {
                String translationPointsLocation = "";
                try
                {
                    this.getDetailsForCurrentApplication();
                    
                    if(applicationDetails.size()>0)
                    {
                        translationPointsLocation = (String)applicationDetails.get("TranslationLocation");
                    }
                    
                    // Assuming the points already exist
                    if((applicationTranslationMapping.size()>0)&&(translationPointsLocation.equals("")))
                    {
                        // done
                        if(this.debug)
                        {
                            System.out.println("[INFO] since Translation Location is empty no download needed");
                        }
                    }
                    else
                    {
                   
                        if(!translationPointsLocation.equals(""))
                        {
                            if(this.debug)
                            {
                                System.out.println("[INFO] since Translation Location is NOT empty we will download from here:"+translationPointsLocation);
                            }
                        }
                        
                        if(applicationDetails.size()>0)
                        {
                            // Find mapping among the details
                            if(applicationDetails.containsKey("TranslationMapping"))
                            {
                                // Sweet! lets load it into the global object
                                applicationTranslationMapping = (MultiMap) applicationDetails.get("TranslationMapping");
                            
                                // if no error then done
                                if((applicationTranslationMapping.size() == 0)||(!translationPointsLocation.equals("")))
                                {
                                    // Set DOWNLOAD to ResourceLocation - xml
                                    if(applicationDetails.containsKey("TranslationLocation"))
                                    {
                                        DOWNLOAD = translationPointsLocation;
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] downloading from Translation Mapping from:"+DOWNLOAD);
                                        }
                                    }
                                    
                                    IslandTravelState = islandTravelAgent.GET;
                                    UserDownload = "";
                                    UserDataDownload = null;
                                
                                    // Get xml file from server loc
                                    while((IslandTravelState == islandTravelAgent.GET)||(UserDownload==""))
                                    {
                                        //synchronized(getLockObject())
                                        //{
                                            if(!_islandLinksIsRunning)
                                            {
                                                _islandLinksIsRunning = true;
                                                
                                                _islandConnectionManager = new IslandLinksConnectThread();
                                                _islandConnectionManager.start();
                                                
                                                IslandTravelState = islandTravelAgent.GET;
                                            }
                                            
                                            synchronized(getLockObject())
                                            {
                                                // Let IslandTravel Manager Know We Wanna download something else
                                                getLockObject().notify();
                                                
                                                // Wait for a response from the IslandTravel Manager
                                                getLockObject().wait(180000);
                                            }
                                        //}
                                    }
                                    
                                    updateDisplay("[INFO] downloaded Translation File Listing",DISPLAY_IN_TEXT);
                                    
                                    // TODO: save xml as resource(s) in mem/local
                                    this.saveApplicationResource(islandApplicationResource.XML_TRANSLATION_LIST);
        
                                    // unpack and fill resource object xml
                                    if(this.applicationItems.UnpackXmlTranslationList())
                                    {   
                                        
                                        // update application Resources
                                        applicationTranslationMapping.add(new Integer(islandApplicationResource.FILE),applicationItems);
            
                                        //updateDisplay("[INFO] Finished downloading resource Files",DISPLAY_IN_TEXT);
                                        
                                        if(IslandTravelState != islandTravelAgent.DOWNLOADED)
                                        {
                                            IslandTravelState = islandTravelAgent.DOWNLOADED;
                                            
                                            if(IslandTravelState == islandTravelAgent.DOWNLOADED)
                                            {
                                                //synchronized(getLockObject())
                                                //{
                                                    if(!_islandLinksIsRunning)
                                                    {
                                                        _islandLinksIsRunning = true;
                                                        
                                                        _islandConnectionManager = new IslandLinksConnectThread();
                                                        _islandConnectionManager.start();
                                                    }
                                                    
                                                    // Wait for a response from the IslandLinks
                                                    getLockObject().notify();
                                                //}
                                            }
                                        }
                                    }
                                }
                            }
                            else
                            {
                                // Sheesh! now we have to go and get the mapping by sending a command
                                if(serviceCommands.size()>0)
                                {
                                    if(serviceCommands.containsKey("GetTranslatorMapping"))
                                    {
                                        // Set the User Commands
                                        UserCommands = (String) serviceCommands.get("GetTranslatorMapping");
                                    }
                                }
                                
                                IslandTravelState = islandTravelAgent.WAIT_FOR_OUTPUT;
                                UserDownload = "";
                                UserDataDownload = null;
                                
                                while(IslandTravelState == islandTravelAgent.WAIT_FOR_OUTPUT)
                                {
                                    synchronized(getLockObject())
                                    {
                                        if(!_islandLinksIsRunning)
                                        {
                                            _islandLinksIsRunning = true;
                                        
                                            _islandConnectionManager = new IslandLinksConnectThread();
                                            _islandConnectionManager.start();
                                        }
                                    
                                    
                                        // Wait for a response from the IslandLinks
                                        getLockObject().wait(180000);
                                    }
                                }
                                
                                if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                {
                                    // End Island Travel 
                                    IslandTravelState = islandTravelAgent.DONE;
                                    
                                    
                                }
                            }
                        }
                        else
                        {
                            // There are no details and hence no mapping
                           
                        }
                    }
                }
                catch(ClassCastException cex)
                {
                     updateDisplay("[ERROR] CEx - problems getting translator points for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(cex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] CEx - problems getting translator points for loaded Application:"+cex.toString());
                    }
                }
                catch(InterruptedException iex)
                {
                     updateDisplay("[ERROR] IEx - problems getting translator points for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] IEx - problems getting translator points for loaded Application:"+iex.toString());
                    }
                }
                catch(Exception ex)
                {
                    updateDisplay("[ERROR] problems getting translator points for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems getting translator points for loaded Application:"+ex.toString());
                    }
                }
            }
            
            private void getOperationsForCurrentApplication()
            {
                String operationsLocation = "";
                 
                try
                {
                    this.getDetailsForCurrentApplication();
                    
                    if(applicationDetails.size()>0)
                    {
                        operationsLocation = (String)applicationDetails.get("OperationsLocation");
                    }
                    
                    if((applicationOperationMapping.size()>0)&&(operationsLocation.equals("")))
                    {
                        
                    }
                    else
                    {
                        if(!operationsLocation.equals(""))
                        {
                            if(this.debug)
                            {
                                System.out.println("[INFO] since Operations Location is NOT empty we will download from here:"+operationsLocation);
                            }
                        }
                        
                        if(applicationDetails.size()>0)
                        {
                            // Find mapping among the details
                            if(applicationDetails.containsKey("OperationsMapping"))
                            {
                                // Sweet! lets load it into the global object
                                applicationOperationMapping = (MultiMap) applicationDetails.get("OperationsMapping");
                            
                                // Get the xml resource list?
                                if((applicationOperationMapping.size() == 0)||(!operationsLocation.equals("")))
                                {
                                    // Set DOWNLOAD to ResourceLocation - xml
                                    if(applicationDetails.containsKey("OperationsLocation"))
                                    {
                                        DOWNLOAD = (String) applicationDetails.get("OperationsLocation");
                                    }
                                    
                                    IslandTravelState = islandTravelAgent.GET;
                                    UserDownload = "";
                                    UserDataDownload = null;
                                
                                    // Get xml file from server loc
                                    while((IslandTravelState == islandTravelAgent.GET)||(UserDownload.equals("")))
                                    {
                                        //synchronized(getLockObject())
                                        //{
                                            if(!_islandLinksIsRunning)
                                            {
                                                _islandLinksIsRunning = true;
                                                
                                                _islandConnectionManager = new IslandLinksConnectThread();
                                                _islandConnectionManager.start();
                                            
                                                IslandTravelState = islandTravelAgent.GET;
                                    
                                            }
                                            
                                            synchronized(getLockObject())
                                            {
                                                // Let IslandTravel Manager Know We Wanna download something else
                                                getLockObject().notify();
                                                
                                                // Wait for a response from the IslandTravel Manager
                                                getLockObject().wait(180000);
                                            }
                                            
                                        //}
                                    }
                                    
                                    updateDisplay("[INFO] downloaded Operations File Listing",DISPLAY_IN_TEXT);
                                    
                                    // TODO: save xml as resource(s) in mem/local
                                    this.saveApplicationResource(islandApplicationResource.XML_OPERATION_LIST);
        
                                    // unpack and fill resource object xml
                                    if(this.applicationItems.UnpackXmlOperationList())
                                    {
                                        
                                        //DownloadAppEnumeration(this.applicationItems.getOperationEnumeration());
                                        applicationOperationMapping.add(new Integer(islandApplicationResource.FILE),applicationItems);
            
                                        //updateDisplay("[INFO] Finished downloading resource Files",DISPLAY_IN_TEXT);
                                        
                                        if(IslandTravelState != islandTravelAgent.DOWNLOADED)
                                        {
                                            IslandTravelState = islandTravelAgent.DOWNLOADED;
                                            
                                            if(IslandTravelState == islandTravelAgent.DOWNLOADED)
                                            {
                                                //synchronized(getLockObject())
                                                //{
                                                    if(!_islandLinksIsRunning)
                                                    {
                                                        _islandLinksIsRunning = true;
                                                        
                                                        _islandConnectionManager = new IslandLinksConnectThread();
                                                        _islandConnectionManager.start();
                                                    }
                                                    
                                                    // Wait for a response from the IslandLinks
                                                    getLockObject().notify();
                                                //}
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                catch(ClassCastException cex)
                {
                     updateDisplay("[ERROR] CCEx - problems getting operations for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(cex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] CCEx - problems getting operations for loaded Application:"+cex.toString());
                    }
                }
                catch(InterruptedException iex)
                {
                     updateDisplay("[ERROR] IEx - problems getting operations for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] IEx - problems getting operations for loaded Application:"+iex.toString());
                    }
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR] problems getting operations for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems getting operations for loaded Application:"+ex.toString());
                        ex.printStackTrace();
                    }
                }
            }
            
            private void getAddressPointsForCurrentApplication()
            {
                String addressPointsLocation = "";
                
                try
                {
                    this.getDetailsForCurrentApplication();
                    
                    if(applicationDetails.size()>0)
                    {
                        addressPointsLocation = (String)applicationDetails.get("AddressesLocation");
                    }
                    
                    if((applicationAddressValues.size()>0)&&(addressPointsLocation.equals("")))
                    {
                        if(this.debug)
                        {
                            System.out.println("[INFO] since Addresses Location is empty no download needed");
                        }
                    }
                    else
                    {
                        if(!addressPointsLocation.equals(""))
                        {
                            if(this.debug)
                            {
                                System.out.println("[INFO] since Addresses Location is NOT empty we will download from here:"+addressPointsLocation);
                            }
                        }
                        
                        if(applicationDetails.size()>0)
                        {
                            // Find mapping among the details
                            if(applicationDetails.containsKey("AddressList"))
                            {
                                // Sweet! lets load it into the global object
                                applicationAddressValues = (MultiMap) applicationDetails.get("AddressList");
                            
                                // Get the xml resource list?
                                if((applicationAddressValues.size() == 0)||(!addressPointsLocation.equals("")))
                                {
                                    // Set DOWNLOAD to ResourceLocation - xml
                                    if(applicationDetails.containsKey("AddressList"))
                                    {
                                        DOWNLOAD = (String) applicationDetails.get("AddressesLocation");
                                    }
                                    
                                    IslandTravelState = islandTravelAgent.GET;
                                    UserDownload = "";
                                    UserDataDownload = null;
                                
                                    // Get xml file from server loc
                                    while((IslandTravelState == islandTravelAgent.GET)||(UserDownload==""))
                                    {
                                        //synchronized(getLockObject())
                                        //{
                                            if(!_islandLinksIsRunning)
                                            {
                                                _islandLinksIsRunning = true;
                                                
                                                _islandConnectionManager = new IslandLinksConnectThread();
                                                _islandConnectionManager.start();
                                                
                                                IslandTravelState = islandTravelAgent.GET;
                                            }
                                            
                                            synchronized(getLockObject())
                                            {
                                                // Let IslandTravel Manager Know We Wanna download something else
                                                getLockObject().notify();
                                                
                                                // Wait for a response from the IslandTravel Manager
                                                getLockObject().wait(180000);
                                            }
                                        //}
                                    }
                                    
                                    updateDisplay("[INFO] downloaded Addresses File Listing",DISPLAY_IN_TEXT);
                                    
                                    // TODO: save xml as resource(s) in mem/local
                                    this.saveApplicationResource(islandApplicationResource.XML_ADDRESS_LIST);
        
                                    // unpack and fill resource object xml
                                    if(this.applicationItems.UnpackXmlAddressList())
                                    {   
                                        
                                        // update application Resources
                                        applicationAddressValues.add(new Integer(islandApplicationResource.FILE),applicationItems);
            
                                        //updateDisplay("[INFO] Finished downloading resource Files",DISPLAY_IN_TEXT);
                                        
                                        if(IslandTravelState != islandTravelAgent.DOWNLOADED)
                                        {
                                            IslandTravelState = islandTravelAgent.DOWNLOADED;
                                            
                                            if(IslandTravelState == islandTravelAgent.DOWNLOADED)
                                            {
                                                //synchronized(getLockObject())
                                                //{
                                                    if(!_islandLinksIsRunning)
                                                    {
                                                        _islandLinksIsRunning = true;
                                                        
                                                        _islandConnectionManager = new IslandLinksConnectThread();
                                                        _islandConnectionManager.start();
                                                    }
                                                    
                                                    // Wait for a response from the IslandLinks
                                                    getLockObject().notify();
                                                //}
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
            
                }
                catch(ClassCastException cex)
                {
                     updateDisplay("[ERROR] CCEx - problems getting addressses for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(cex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] CCEx - problems getting addresses for loaded Application:"+cex.toString());
                    }
                }
                catch(InterruptedException iex)
                {
                     updateDisplay("[ERROR] IEx - problems getting addresses for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] IEx - problems getting addressess for loaded Application:"+iex.toString());
                    }
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR] problems getting addresses for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems getting addresses for loaded Application:"+ex.toString());
                        ex.printStackTrace();
                    }
                }
            }
                
            
            private void getResourcesForCurrentApplication()
            {
                String resourceLocation = "";
                
                try
                {
                        this.getDetailsForCurrentApplication();

                        if(applicationDetails.size()>0)
                        {
                            resourceLocation = (String)applicationDetails.get("ResourcesLocation");
                        }

                        if(applicationDetails.size()>0)
                        {
                            // Find mapping among the details
                            if(applicationDetails.containsKey("ResourcesList"))
                            {
                                // Sweet! lets load it into the global object
                                applicationResourceValues = (MultiMap) applicationDetails.get("ResourcesList");
                            
                                // Get the xml resource list? 
                                if((applicationResourceValues.size() == 0)||(!resourceLocation.equals("")))
                                {
                                    // Set DOWNLOAD to ResourceLocation - xml
                                    if(applicationDetails.containsKey("ResourcesLocation"))
                                    {
                                        DOWNLOAD = (String) applicationDetails.get("ResourcesLocation");
                                        
                                        if(DOWNLOAD.indexOf("XID")>=0)
                                        {
                                            DOWNLOAD += "&Hght=" +  _mainScreen.getHeight() + "&Width=" + _mainScreen.getWidth();
                                        }
                                    }
                                    
                                    IslandTravelState = islandTravelAgent.GET;
                                    UserDownload = "";
                                    UserDataDownload = null;
                                
                                    // Get xml file from server loc
                                    while((IslandTravelState == islandTravelAgent.GET)||(UserDownload.equals("")))
                                    {
                                        //synchronized(getLockObject())
                                        //{
                                            if(!_islandLinksIsRunning)
                                            {
                                                _islandLinksIsRunning = true;
                                                
                                                _islandConnectionManager = new IslandLinksConnectThread();
                                                _islandConnectionManager.start();
                                                
                                                IslandTravelState = islandTravelAgent.GET;

                                            }
                                            
                                             synchronized(getLockObject())
                                            {
                                                // Let IslandTravel Manager Know We Wanna download something else
                                                getLockObject().notify();
                                                
                                                // Wait for a response from the IslandTravel Manager
                                                getLockObject().wait(180000);
                                            }
                                        //}
                                    }
                                    
                                    updateDisplay("[INFO] downloaded Resource File Listing",DISPLAY_IN_TEXT);
                                    
                                    // TODO: save xml as resource(s) in mem/local
                                    this.saveApplicationResource(islandApplicationResource.XML_GUI_RESOURCE_LIST);
        
                                    //this.saveApplicationResource(islandApplicationResource.XML_DATA_RESOURCE_LIST);
        
                                    //this.saveApplicationResource(islandApplicationResource.XML_MEDIA_RESOURCE_LIST);
                                
                         
                                    // unpack and fill resource object xml
                                    if(this.applicationItems.UnpackXmlGuiResourceList()||
                                        this.applicationItems.UnpackXmlMediaResourceList()||
                                        this.applicationItems.UnpackXmlDataResourceList())
                                    {
                                        
                                        DownloadAppEnumeration(this.applicationItems.getGuiResourceEnumeration());
                                        
                                        //DownloadAppEnumeration(this.applicationItems.getDataResourceEnumeration());
                                        
                                        //DownloadAppEnumeration(this.applicationItems.getMediaResourceEnumeration());
                                        
                                        updateDisplay("[INFO] Finished downloading resource Files",DISPLAY_IN_TEXT);
                                        
                                        if(IslandTravelState != islandTravelAgent.DOWNLOADED)
                                        {
                                            IslandTravelState = islandTravelAgent.DOWNLOADED;
                                            
                                            if(IslandTravelState == islandTravelAgent.DOWNLOADED)
                                            {
                                                //synchronized(getLockObject())
                                                //{
                                                    if(!_islandLinksIsRunning)
                                                    {
                                                        _islandLinksIsRunning = true;
                                                        
                                                        _islandConnectionManager = new IslandLinksConnectThread();
                                                        _islandConnectionManager.start();
                                                    }
                                                    
                                                    // Wait for a response from the IslandLinks
                                                    getLockObject().notify();
                                                //}
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    // just get App=>Details=>ResourceList=>ResourceValues=>ResourceObject
                                    this.saveApplicationResource(islandApplicationResource.STATIC_GUI_RESOURCE_LIST);
                                    
                                    //this.saveApplicationResource(islandApplicationResource.STATIC_MEDIA_RESOURCE_LIST);
                                    
                                    //this.saveApplicationResource(islandApplicationResource.STATIC_DATA_RESOURCE_LIST);
                                    
                                    // unpack and fill resource object xml
                                    if(this.applicationItems.UnpackStaticGuiResourceList(applicationResourceValues, applicationTranslationMapping)||
                                        this.applicationItems.UnpackStaticDataResourceList(applicationResourceValues, applicationTranslationMapping)||
                                        this.applicationItems.UnpackStaticMediaResourceList(applicationResourceValues, applicationTranslationMapping))
                                    {
                                        Enumeration aire = this.applicationItems.getGuiResourceEnumeration();
                                        
                                        try
                                        {
                                            // Enumerate and get resource
                                            for (Enumeration e = aire; e.hasMoreElements();) 
                                            {
                                                
                                                Object element = e.nextElement();
                                                
                                                if(element instanceof String)
                                                {
                                                    // Set resource loc 
                                                    DOWNLOAD = (String)element;
                                                    
                                                    // replace resource loc with resource Value
                                                    this.getApplicationResource();
                                                    
                                                    updateDisplay("[INFO] downloaded Needed resource File",DISPLAY_IN_TEXT);                                   
                                                    
                                                    if(this.debug)
                                                    {
                                                        System.out.println(element);
                                                    }
                                                }
                                                
                                                if(element instanceof Hashtable)
                                                {
                                                    for(Enumeration he = ((Hashtable)element).elements(); he.hasMoreElements();)
                                                    {
                                                        // Set resource loc 
                                                        DOWNLOAD = (String)he.nextElement();
                                                        
                                                        if(this.debug)
                                                        {
                                                            System.out.println("about to get:"+DOWNLOAD);
                                                        }
                                                      
                                                        if(DOWNLOAD.length()>0)
                                                        {
                                                            // replace resource loc with resource Value
                                                            this.getApplicationResource();
                                                            
                                                            //updateDisplay("[INFO] downloaded Needed resource Files",DISPLAY_IN_TEXT);                                   
                                                        }
                                                    }                                                        
                                                }
                                            }
                                            
                                            updateDisplay("[INFO] Finished downloading resource Files",DISPLAY_IN_TEXT);
                                        }
                                        catch(Exception ex)
                                        {
                                            if(this.debug)
                                            {
                                                System.out.println("[ERROR] problems getting application Resource:"+ex.toString());
                                            }
                                        }
                                        
                                        //updateDisplay("[INFO] Finished downloading resource Files",DISPLAY_IN_TEXT);
                                        
                                        if(IslandTravelState != islandTravelAgent.DOWNLOADED)
                                        {
                                            IslandTravelState = islandTravelAgent.DOWNLOADED;
                                            
                                            if(IslandTravelState == islandTravelAgent.DOWNLOADED)
                                            {
                                                //synchronized(getLockObject())
                                                //{
                                                    if(!_islandLinksIsRunning)
                                                    {
                                                        _islandLinksIsRunning = true;
                                                        
                                                        _islandConnectionManager = new IslandLinksConnectThread();
                                                        _islandConnectionManager.start();
                                                    }
                                                    
                                                    // Wait for a response from the IslandLinks
                                                    getLockObject().notify();
                                                //}
                                            }
                                        }
                                    }
                                }
                                // if no error then done
                            }
                            else
                            {
                                // Sheesh! now we have to go and get the mapping by sending a command
                                if(serviceCommands.size()>0)
                                {
                                    if(serviceCommands.containsKey("GetApplicationResources"))
                                    {
                                        // Set the User Commands
                                        UserCommands = (String) serviceCommands.get("GetApplicationResources");
                                    }
                                    else
                                    {
                                        throw new Exception("No service command to get Application Resource!");
                                    }
                                }
                                
                                IslandTravelState = islandTravelAgent.WAIT_FOR_OUTPUT;
                                
                                while(IslandTravelState == islandTravelAgent.WAIT_FOR_OUTPUT)
                                {
                                    if(!_islandLinksIsRunning)
                                    {
                                        _islandLinksIsRunning = true;
                                        
                                        _islandConnectionManager = new IslandLinksConnectThread();
                                        _islandConnectionManager.start();
                                    }
                                    
                                    // Wait for a response from the IslandLinks
                                    getLockObject().wait(180000);
                                }
                                
                                if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                {
                                    // End Island Travel 
                                    IslandTravelState = islandTravelAgent.DONE;
                                }
                            }
                        }
                    //}
                }
                catch(ClassCastException cex)
                {
                     updateDisplay("[ERROR] CCEx - problems getting resources for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(cex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] CCEx - problems getting resources for loaded Application:"+cex.toString());
                    }
                }
                catch(InterruptedException iex)
                {
                     updateDisplay("[ERROR] IEx - problems getting resources for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] IEx - problems getting resources for loaded Application:"+iex.toString());
                    }
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR] problems getting resources for loaded Application",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems getting resources for loaded Application:"+ex.toString());
                        ex.printStackTrace();
                    }
                }
            }
            
            private void notifyApplication()
            {
                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_DETAILS)
                {
                    IslandSoftwareState = islandSoftwareEngine.APP_DETAILS_READY;
                }
                
                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_TRANSLATION_POINTS)
                {
                    IslandSoftwareState = islandSoftwareEngine.APP_TRANSLATION_POINTS_READY;
                }

                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_ADDRESS_POINTS)
                {
                    IslandSoftwareState = islandSoftwareEngine.APP_ADDRESS_POINTS_READY;
                }

                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_OPERATIONS_DOWNLOAD)
                {
                    IslandSoftwareState = islandSoftwareEngine.APP_OPERATIONS_DOWNLOADED;
                }

                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_RESOURCES_DOWNLOAD)
                {
                    IslandSoftwareState = islandSoftwareEngine.APP_RESOURCES_DOWNLOADED;
                }

                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_DOWNLOAD)
                {
                    IslandSoftwareState = islandSoftwareEngine.APP_DOWNLOAD_COMPLETED;
                }
                
                getLockObject().notifyAll();
            }
            
            /**
             * function to throw up form dialog to enter
             * data from the user.
             */
            private void getUserInput()
            {
                updateDisplay(UserMessage,DISPLAY_IN_DIALOG_FORM_TEXT);
            }
            
            public void notifyIslandLinks()
            {
                if(IslandTravelState == islandTravelAgent.RECIEVE_COMMAND)
                {
                    if( (IslandSoftwareState !=islandSoftwareEngine.APP_ENGINE_STARTED)&&
                        (IslandSoftwareState !=islandSoftwareEngine.KERNEL_NETWORK)  &&
                        (IslandSoftwareState != islandSoftwareEngine.WAIT_FOR_ENGINE_OPERATION) &&
                        (IslandSoftwareState != islandSoftwareEngine.APP_OPERATION_COMPLETED) 
                      )
                    {
                        IslandTravelState = islandTravelAgent.SEND_COMAND;
                    }
                }
                
                if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                {
                    if( (IslandSoftwareState != islandSoftwareEngine.APP_ENGINE_STARTED)&&
                        (IslandSoftwareState != islandSoftwareEngine.KERNEL_NETWORK) &&
                        (IslandSoftwareState != islandSoftwareEngine.WAIT_FOR_ENGINE_OPERATION)&& 
                        (IslandSoftwareState != islandSoftwareEngine.APP_OPERATION_COMPLETED) 
                      )
                    {
                        if(this.debug)
                        {
                            System.out.println("[INFO] this is the Software State:"+IslandSoftwareState);
                        }
                        
                        IslandTravelState = islandTravelAgent.WAIT_FOR_OUTPUT;
                    }
                }
                
                getLockObject().notify();
            }
            
            /**
            * Implementation of Thread.
            */
            public void run()
            {
                
                try
                {
                    while(_applicationIsRunning||_islandLinksIsRunning||_homeIslandIsRunning)
                    {
                            // Software state events
                            try 
                            {
                                synchronized(getLockObject())
                                {
                                    _homeIslandIsRunning = true;
                                }
                                           
                                if(this.debug)
                                {
                                    //System.out.println("[INFO] The current IslandSoftwareState is:"+IslandSoftwareState);
                                }            
                                            
                                // Wait for commands from the application thread
                                if(_applicationIsRunning)
                                {
                                    if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_DETAILS)
                                    {
                                        
                                         // Handle waiting threads
                                        synchronized(getLockObject())
                                        {
                                            
                                            // complete
                                            getDetailsForCurrentApplication();
                                        
                                            // activate waiting thread
                                            notifyApplication();
                                            //yield();
                                        
                                        }
                                    }
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_TRANSLATION_POINTS)
                                    {
                                         // Handle waiting threads
                                        synchronized(getLockObject())
                                        {
                                            // complete
                                            getTranslatorPointsForCurrentApplication();
                                
                                            // activate waiting thread
                                            notifyApplication();
                                        }
                                    }
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_OPERATIONS_DOWNLOAD)
                                    {
                                         // Handle waiting threads
                                        synchronized(getLockObject())
                                        {
                                            // complete
                                            getOperationsForCurrentApplication();
                                
                                            // activate waiting thread
                                            notifyApplication();
                                        }
                                    }
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_ADDRESS_POINTS)
                                    {
                                         // Handle waiting threads
                                        synchronized(getLockObject())
                                        {
                                            // complete
                                            getAddressPointsForCurrentApplication();
                                
                                            // activate waiting thread
                                            notifyApplication();
                                        }
                                    }
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_RESOURCES_DOWNLOAD)
                                    {
                                        
                                        // Handle waiting threads
                                        synchronized(getLockObject())
                                        {
                                            // complete
                                            getResourcesForCurrentApplication();
                                
                                            // activate waiting thread
                                            notifyApplication();
                                           
                                        }
                                    }
                                }
                            } 
                            catch ( Exception ie ) 
                            {
                                updateDisplay("[ERROR] completing application Request.",DISPLAY_IN_TEXT);
                                
                                if(debug)
                                {
                                    updateDisplay(ie.toString(),DISPLAY_IN_TEXT);
                                    System.out.println("[ERROR] problems completed Application Request:"+ie.toString());
                                }
                            }
                        
                            // Island Communication state events
                            try
                            {
                                // Wait for commands from the other threads
                                if(_islandLinksIsRunning)
                                {
                                    if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                    {
                                        if(IslandSoftwareState != islandSoftwareEngine.APP_ENGINE_STARTED)
                                        {
                                            // Handle waiting threads
                                            synchronized(getLockObject())
                                            {
                                                // complete
                                                getUserInput();
                                                
                                                // activate waiting threads
                                                notifyIslandLinks();
                                            }
                                        }
                                    }
                                }
                            }
                            catch( Exception ie )
                            {
                                updateDisplay("[ERROR] interacting with the user for island Links", DISPLAY_IN_TEXT);
                                
                                if(debug)
                                {
                                    updateDisplay(ie.toString(),DISPLAY_IN_TEXT);
                                    System.out.println("[ERROR] problems interacting with the user for island Links:"+ie.toString());
                                }
                            }
                        
                            // Home Island Events
                            try
                            {
                                // open default dialog wizard
                                if((!_islandLinksIsRunning)&&(!_applicationIsRunning))
                                {
                                    if(!_homeIslandIsRunning)
                                    {
                                        _homeIslandIsRunning = true;
                                    }
                                    
                                    // load user info and profile
                                    getDetailsForCurrentUser();
                                    
                                    if(_userDetailsLoaded)
                                    {
                                        if(_profileLoaded)
                                        {
                                            // if we are to run services
                                            if(IslandResourceState == islandUserResource.USER_SERVICES)
                                            {
                                                synchronized(getLockObject())
                                                {
                                                    // show all the services we can run on this thing
                                                    showUserServices();
                                                }
                                            }
                                            
                                            // if we are to run applications
                                            if(IslandResourceState == islandUserResource.USER_APPLICATIONS)
                                            {
                                                synchronized(getLockObject())
                                                {
                                                    // show available applications
                                                    showUserApplications();
                                                }
                                            }
                                            
                                            // if we are to use or subscriptions
                                            if(IslandResourceState == islandUserResource.USER_SUBSCRIPTIONS)
                                            {
                                                synchronized(getLockObject())
                                                {
                                                    // show available subscriptions
                                                    showUserSubscriptions();
                                                }
                                            }
                                        }
                                        else
                                        {
                                            // Handle waiting threads
                                            synchronized(getLockObject())
                                            {
                                                // display profile Gui to edit
                                                showProfile();
                                            }
                                        }
                                    }
                                    else
                                    {
                                        // Handle waiting threads
                                        synchronized(getLockObject())
                                        {
                                            // get user info
                                            showUserInfo();
                                        }
                                    }
                                } 
                            }
                            catch(Exception ie)
                            {
                                updateDisplay("[ERROR] managing profile wizard", DISPLAY_IN_TEXT);
                            
                                if(debug)
                                {
                                    updateDisplay(ie.toString(),DISPLAY_IN_TEXT);
                                    System.out.println("[ERROR] problems managing rofile wizard:"+ie.toString());
                                }
                            
                            }
                            
                            if((!_islandLinksIsRunning)&&(!_applicationIsRunning))
                            {
                                _homeIslandIsRunning = false;
                                join(); //with main thread
                            }
                            else
                            {
                                if(!_applicationIsRunning)
                                {
                                    if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_USER_ACTION)
                                    {
                                        join();
                                    }
                                    else
                                    {
                                        yield();
                                    }
                                }
                                
                                if(!_islandLinksIsRunning)
                                {
                                    yield();
                                }
                                
                                if(_applicationIsRunning)
                                {
                                    if(IslandSoftwareState == islandSoftwareEngine.APP_DOWNLOAD_COMPLETED)
                                    {
                                        // wait until we are needed again
                                        synchronized(getLockObject())
                                        {
                                            getLockObject().wait(180000);
                                        }
                                    }
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_ENGINE_START)
                                    {
                                        // wait until we are needed again
                                        synchronized(getLockObject())
                                        {
                                            getLockObject().wait(180000);
                                        }
                                    }
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.APP_ENGINE_STARTED)
                                    {
                                        // wait until we are needed again
                                        synchronized(getLockObject())
                                        {
                                            getLockObject().wait(180000);
                                        }
                                    }
                                }
                            }
                        }       
                    
                }
                catch(Exception e)
                {
                     updateDisplay("[ERROR] problems running Home Island Thread",DISPLAY_IN_TEXT);
                    
                    if(debug)
                    {
                        updateDisplay(e.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems running Home Island Thread:"+e.toString());
                    }
                }
            }
        }
    
        /**
        * A private inner class to manage the Island Application Types.
        * Not static since we access some methods in our parent. This thread
        * knows how to run island Apps talking to the Home Island for resources
        * and details loaded off the wire. Application can use the global objects
        * and states from the Home Island, but can also save locally to disk.
        */
        private class IslandApplicationThread extends Thread
        {
            // class flags
            private boolean debug;
            
            // application attribute flags
            private boolean applicationUsesTranslator;
            private boolean applicationUsesAddresses;
            private boolean applicationDefinesGui;
            private boolean applicationHasLayout;
            private boolean applicationHasResources;
            private boolean applicationHasProcOps;
            private boolean applicationRunsLocal;
            private boolean applicationRunsOnline;
            private boolean applicationRunsOnlinks;
            private boolean applicationNeedsAuth;
           
            private boolean appTranslationLocal;
            private boolean appTranslationOnline;
            private boolean appTranslationOnlinks;
            
            private boolean appResourcesLocal;
            private boolean appResourcesOnline;
            private boolean appResourcesOnlinks;
            
            private boolean appAddressesLocal;
            private boolean appAddressesOnline;
            private boolean appAddressesOnlinks;
            
            private boolean appOperationsLocal;
            private boolean appOperationsOnline;
            private boolean appOperationsOnlinks;
            
            // application save attributes
            private boolean savingApplicationStateToDisk;
            private boolean savingOperationsToDisk;
            private boolean savingAddressesToDisk;
            private boolean savingTranslationsToDisk;
            private boolean savingResourcesToDisk;
            
            // application state flags
            private boolean guiAssembled;
            private boolean layoutsAssembled;
            private boolean gridAssembled;
            private boolean loadedAppStarted;
            
            // in memory storage
            private MultiMap applicationTranslatorPoints = null;
            private MultiMap applicationResourcePoints = null;
            private MultiMap applicationAddressPoints = null;
            private MultiMap applicationOperationPoints = null;
            
            // Application Engine
            public islandSoftwareEngine ApplicationEngine = null;
            public islandProcessorEngine ProcessorEngine = null;
            
            public boolean preferencesLoaded = false;
            public boolean mapsLoaded = false; 
            public String currentGrid = "";
            
            public String translationLocation;
            public String resourcesLocation;
            public String addressesLocation;
            public String operationsLocation;
            
            public IslandApplicationThread()
            {
                this.debug = true;
                this.applicationTranslatorPoints = new MultiMap();
                this.applicationResourcePoints = new MultiMap();
                this.applicationAddressPoints = new MultiMap();
                this.applicationOperationPoints = new MultiMap();
                
                this.savingTranslationsToDisk = false;
                this.savingResourcesToDisk = false;
            
                this.appTranslationLocal = false;
                this.appTranslationOnline = false;
                this.appTranslationOnlinks = false;
            
                this.appResourcesLocal = false;
                this.appResourcesOnline = false;
                this.appResourcesOnlinks = false;
            
                this.appAddressesLocal = false;
                this.appAddressesOnline = false;
                this.appAddressesOnlinks = false;
            
                this.appOperationsLocal = false;
                this.appOperationsOnline = false;
                this.appOperationsOnlinks = false;
            
                this.translationLocation = "";
                this.resourcesLocation = "";
                this.addressesLocation = "";
                this.operationsLocation = "";
            }
         
            public void SetDebug(boolean d)
            {
                this.debug = d;
            } 
            
            public void SetTranslationLocation(String loc)
            {
                this.translationLocation = loc;
            }
            
            public void SetAddressPointsLocation(String loc)
            {
                this.addressesLocation = loc;
            }
            
            public void SetOperationsLocation(String loc)
            {
                this.operationsLocation = loc;
            }
            
            public void SetResourcesLocation(String loc)
            {
                this.resourcesLocation = loc;
            }
            
            public void SetCurrentGrid(String grid)
            {
                currentGrid = grid;
            }
            
            public void SetCurrentGrid(islandSoftwareEngine.GridScreenDetails grid)
            {
                currentGrid = grid.GridName;
            }
            
            private boolean loadApplication()
            {
                boolean appLoaded = false;
                
                // Get Default Application Details
                getApplicationDetails();
            
                // Load Translator Points if the app needs them
                if(applicationUsesTranslator)
                {
                    // Make sure multi map has necessary data 
                    if(allTranslatorPointsLoaded())
                    {
                        // Load Addresses
                        if(applicationUsesAddresses)
                        {
                            // Make sure that MultiMap has necessary data
                            if(allAddressPointsLoaded())
                            {
                                // Load Application Operations
                                if(applicationHasProcOps)
                                {
                                    // Make sure multi map has necessary data
                                    if(allAppOperationsLoaded())
                                    {
                                        // Load Application Layout
                                        if(applicationHasLayout)
                                        {
                                            // Make sure MultiMap has necessary data
                                            if(allAppLayoutsLoaded())
                                            {
                                                // Load Application resources if there are any
                                                if(applicationHasResources)
                                                {
                                                    // Make sure multi map has necessary data 
                                                    if(allAppResourcesLoaded())
                                                    {
                                                        // this is loaded already!
                                                        appLoaded = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                // if it doesn't use a translator then no Kernel Calls or Stack    
                else
                {
                    if(applicationHasLayout)
                    {
                        if(allAppLayoutsLoaded())
                        {
                            // Load Application resources if there are any
                            if(applicationHasResources)
                            {
                                // Make sure multi map has necessary data 
                                if(allAppResourcesLoaded())
                                {
                                    // this is loaded already!
                                    appLoaded = true;
                                }
                            }
                        }
                    }
                }
                
                // Assemble Gui it is defined 
                if(applicationDefinesGui)
                {
                    if(gridLayoutsNeedAssembling())
                    {
                        if(guiNeedsAssembling())
                        {
                            // Add the graphics to the layouts
                            //this.ApplicationEngine.AddGuiObjectsToLayouts();
                            
                            guiAssembled = true;
                        }
                    }
                    else
                    {    
                        // load gui objects into memory
                        if(guiNeedsAssembling())
                        {
                            
                            if(ApplicationEngine.HasGraphics())
                            {
                                //ApplicationEngine.FillGraphicResourceDetail();
                                
                                if(ApplicationEngine.HasComponents())
                                {
                                    
                                }
                                else
                                {
                                    // add necessary Graphic Objects to the Main Screen
                                    //_mainScreen.replace(_graphicsManager,(VerticalFieldManager) ApplicationEngine.AddGraphicResourcesToManager(null));
                                }
                            }
                            else
                            {
                                if(ApplicationEngine.HasComponents())
                                {
                                    // re-add at index 0
                                    //_graphicsManager.replace(_componentsManager,(VerticalFieldManager)ApplicationEngine.AddGuiObjectsToManager());
                                }
                            }
                            
                            // Set flag when finished
                            guiAssembled = true;
                        }
                    
                    }
                }
                
                if(this.applicationHasProcOps)
                {
                    stackNeedsAssembling();
                }
                
                return appLoaded;
            }
            
            /**
             * Get mapping out of Resources and put it
             * into the local MultiMap. Then copy Map
             * over to the engine.
             * @return <description>
             */
            private boolean guiNeedsAssembling()
            {
                boolean multiMapsLoaded = false;
                
                try
                {
                    /* Data is Labelled and Placement and Events Include Labels*/
                    if(this.applicationHasResources)
                    {
                        if(this.debug)
                        {
                            System.out.println("[INFO] processing Resources for gui");
                        }
                        
                        if(this.applicationResourcePoints.size(new Integer(islandApplicationResource.FILE))>0)
                        {
                            // Load Application File Elements
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements(new Integer(islandApplicationResource.FILE)),islandApplicationResource.FILE);
                        }

                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_ICON_GRAPHIC))>0)
                        {
                            // Load Application Icon
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements("Icon"),islandSoftwareEngine.APP_ICON_GRAPHIC);
                        }
                        
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_BACKGROUND_GRAPHIC))>0)
                        {
                            // Load Application Background Image
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements("BackgroundImage"),islandSoftwareEngine.APP_BACKGROUND_GRAPHIC);
                        }
                        
                        // example como=> this.applicationEngine.SetGiuMapElements(this.applicationResourcePoints.elements("BackgroundImage[1]_0:0"),islandSoftwareEngine.APP_GRAPHIC_PLACEMENT);
                        
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_PNG_GRAPHIC))>0)
                        {
                            // Load Application Image
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements("PngImages"),islandSoftwareEngine.APP_PNG_GRAPHIC);
                        }
                        
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_BMP_GRAPHIC))>0)
                        {
                            // Load Application Image
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements("BmpImages"),islandSoftwareEngine.APP_BMP_GRAPHIC);
                        }
                        
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_TEXT_GRAPHIC))>0)
                        {
                           // Load Application Image
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements("Text"),islandSoftwareEngine.APP_TEXT_GRAPHIC);
                        }
                        
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_PLACEMENT))>0)
                        {
                            // Load GUI Resource Placement
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_PLACEMENT)),islandSoftwareEngine.APP_GRAPHIC_PLACEMENT);
                        
                            // Flaten double values to int
                            //this.ApplicationEngine.FlattenGraphicPlacements();
                        
                        }
                    
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_LOCATION))>0)
                        {
                            // Load GUI Resource Location
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_LOCATION)),islandSoftwareEngine.APP_GRAPHIC_LOCATION);
                        }
                        
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_EVENT))>0)
                        {
                            // Load GUI event calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_EVENT)), islandSoftwareEngine.APP_GRAPHIC_EVENT);
                        }
                        
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_WIDTH))>0)
                        {
                            // Load GUI Widths calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_WIDTH)), islandSoftwareEngine.APP_GRAPHIC_WIDTH);
                        }
                    
                        if(this.applicationResourcePoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_HEIGHT))>0)
                        {
                            // Load GUI Widths calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationResourcePoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_HEIGHT)), islandSoftwareEngine.APP_GRAPHIC_HEIGHT);
                        }
                    }
                    
                    if(this.applicationUsesTranslator)
                    {
                        if(this.debug)
                        {
                            System.out.println("[INFO] processing Translation for gui");
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandApplicationResource.FILE))>0)
                        {
                            // Load GUI placement calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandApplicationResource.FILE)),islandApplicationResource.FILE);
                        
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GUI_PLACEMENT))>0)
                        {
                            // Load GUI placement calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GUI_PLACEMENT)),islandSoftwareEngine.APP_GUI_PLACEMENT);
                        
                            // Flaten double values to int
                            //this.ApplicationEngine.FlattenGuiPlacements();
                        }
                    
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GUI_PLACEMENTS))>0)
                        {
                            // Load GUI placements calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GUI_PLACEMENTS)),islandSoftwareEngine.APP_GUI_PLACEMENTS);
                        
                            // Flaten double values to int
                            //this.ApplicationEngine.FlattenGuiPlacements();
                        
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GUI_HEIGHT))>0)
                        {
                            // Load GUI placement calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GUI_HEIGHT)),islandSoftwareEngine.APP_GUI_HEIGHT);
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GUI_WIDTH))>0)
                        {
                            // Load GUI placement calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GUI_WIDTH)),islandSoftwareEngine.APP_GUI_WIDTH);
                        }
                    
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GUI_EVENT))>0)
                        {
                            // Load GUI event calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GUI_EVENT)), islandSoftwareEngine.APP_GUI_EVENT);
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GUI_COLOR))>0)
                        {
                            // Load GUI color calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GUI_COLOR)), islandSoftwareEngine.APP_GUI_COLOR);
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_PLACEMENT))>0)
                        {
                            // Load GUI Resource Placement
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_PLACEMENT)),islandSoftwareEngine.APP_GRAPHIC_PLACEMENT);
                            
                            // Flaten double values to int
                            //this.ApplicationEngine.FlattenGraphicPlacements();
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_PLACEMENTS))>0)
                        {
                            // Load GUI Resource Placements
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_PLACEMENTS)),islandSoftwareEngine.APP_GRAPHIC_PLACEMENTS);
                            
                            // Flaten double values to int
                            //this.ApplicationEngine.FlattenGraphicPlacements();
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_HEIGHT))>0)
                        {
                            // Load GUI placement calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_HEIGHT)),islandSoftwareEngine.APP_GRAPHIC_HEIGHT);
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_WIDTH))>0)
                        {
                            // Load GUI placement calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_WIDTH)),islandSoftwareEngine.APP_GRAPHIC_WIDTH);
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_LOCATION))>0)
                        {
                            // Load GUI Resource Placement
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_LOCATION)),islandSoftwareEngine.APP_GRAPHIC_LOCATION);
                        }
                    
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_EVENT))>0)
                        {
                            // Load GUI event calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_EVENT)), islandSoftwareEngine.APP_GRAPHIC_EVENT);
                        }
                    
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_GRAPHIC_COLOR))>0)
                        {
                            // Load GUI color calls into engine
                            this.ApplicationEngine.SetGuiMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRAPHIC_COLOR)), islandSoftwareEngine.APP_GRAPHIC_COLOR);
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_CONTENT_EVENT))>0)
                        {
                            // Load Content event calls into engine
                            this.ApplicationEngine.SetContentMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_CONTENT_EVENT)), islandSoftwareEngine.APP_CONTENT_EVENT);
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.APP_CONTENT_MANAGEMENT))>0)
                        {
                            // Load Content Management calls into engine
                            this.ApplicationEngine.SetContentMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_CONTENT_MANAGEMENT)), islandSoftwareEngine.APP_CONTENT_MANAGEMENT);
                        }
                        
                        if(this.applicationTranslatorPoints.size(new Integer(islandSoftwareEngine.KERNEL_NETWORK))>0)
                        {
                            // Load Kernel calls into engine
                            this.ApplicationEngine.SetKernelMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.KERNEL_NETWORK)), islandSoftwareEngine.KERNEL_NETWORK);
                        }
                    }
                    
                    multiMapsLoaded = true;
                }
                catch(Exception Ex)
                {
                     updateDisplay("[ERROR] getting the gui assembled.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(Ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] problems getting the gui assembled:"+Ex.toString());
                            Ex.printStackTrace();
                        }
                }
                
                return multiMapsLoaded;
            }
            
            private boolean gridLayoutsNeedAssembling()
            {
                boolean multiMapsLoaded = false;
                
                try
                {
              
                    if(this.applicationHasLayout)
                    {
                        if(this.debug)
                        {
                            System.out.println("[INFO] processing gridLayout for application");
                        }
                        
                        // Load Downloaded File contents
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandApplicationResource.FILE)),islandApplicationResource.FILE);
                        
                        // Load Application Grid Origin
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID)),islandSoftwareEngine.APP_GRID);
                    
                        // Load Application Grid Origin
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID_ORIGIN)),islandSoftwareEngine.APP_GRID_ORIGIN);
                    
                        // Load Application Grid Max
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID_MAX)),islandSoftwareEngine.APP_GRID_MAX);
                    
                        // Load Application Grid Color
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID_COLOR)),islandSoftwareEngine.APP_GRID_COLOR);
                    
                        // Load Application Grid Event
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID_EVENT)),islandSoftwareEngine.APP_GRID_EVENT);
                    
                        // Load Application Grid Layout
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID_LAYOUT)),islandSoftwareEngine.APP_GRID_LAYOUT);
                    
                        // Load Application Grid Layout Origin
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID_LAYOUT_ORIGIN)),islandSoftwareEngine.APP_GRID_LAYOUT_ORIGIN);
                    
                        // Load Application Grid Layout Color
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID_LAYOUT_COLOR)),islandSoftwareEngine.APP_GRID_LAYOUT_COLOR);
                    
                        // Load Application Grid Layout Events
                        this.ApplicationEngine.SetLayoutMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.APP_GRID_LAYOUT_EVENT)),islandSoftwareEngine.APP_GRID_LAYOUT_EVENT);
                    
                        // Set Default Grid Display/Screen
                        this.SetCurrentGrid(this.ApplicationEngine.GetDisplayGridFromIndx(0));
                    }
                
                    multiMapsLoaded = true;
                }                  
                catch(Exception Ex)
                {
                    updateDisplay("[ERROR] getting the grid Layout assembled.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(Ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] problems getting the layout assembled:"+Ex.toString());
                            Ex.printStackTrace();
                        }
                }                    
                return multiMapsLoaded; 
            }
                            
            /**
             * Get mapping out of Addresses and operations 
             * and put it
             * into the local MultiMap. Then copy Map
             * over to the appropriate engine(s).
             * @return <description>
             */
            private boolean stackNeedsAssembling()
            {
                boolean multiMapsLoaded = false;
                
                try
                {
                    
                    if(this.applicationUsesAddresses)
                    {
                        if(this.debug)
                        {
                            System.out.println("[INFO] processing Addresses for stack");
                        }
                        
                        // Load Downloaded File contents
                        this.ApplicationEngine.SetAddressMapElements(this.applicationAddressPoints.elements(new Integer(islandApplicationResource.FILE)),islandApplicationResource.FILE);
                        
                        // Load Address Locations
                        this.ApplicationEngine.SetAddressMapElements(this.applicationAddressPoints.elements(new Integer(islandSoftwareEngine.KERNEL_MEMORY)),islandSoftwareEngine.KERNEL_MEMORY);
                        
                        // Load Kernel Memory from Translation
                        this.ApplicationEngine.SetAddressMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.KERNEL_MEMORY)),islandSoftwareEngine.KERNEL_MEMORY);
                    
                    
                        // Load Kernel Io
                        this.ApplicationEngine.SetKernelMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.KERNEL_IO)),islandSoftwareEngine.KERNEL_IO);
                        
                        // Load Vendor Libraries
                        this.ApplicationEngine.SetKernelMapElements(this.applicationAddressPoints.elements(new Integer(islandSoftwareEngine.APP_VENDOR_LIB)),islandSoftwareEngine.APP_VENDOR_LIB);
                    
                        // Load API Frameworks
                        this.ApplicationEngine.SetKernelMapElements(this.applicationAddressPoints.elements(new Integer(islandSoftwareEngine.APP_API_FRAMEWORK)),islandSoftwareEngine.APP_API_FRAMEWORK);
                        
                        // Load API Functions
                        this.ApplicationEngine.SetKernelMapElements(this.applicationAddressPoints.elements(new Integer(islandSoftwareEngine.APP_API_FUNCTION)),islandSoftwareEngine.APP_API_FUNCTION);
                    
                        // Load Kernel Functions
                        this.ApplicationEngine.SetKernelMapElements(this.applicationAddressPoints.elements(new Integer(islandProcessorEngine.PROCESSOR_KERNEL_FUNCTION)),islandProcessorEngine.PROCESSOR_KERNEL_FUNCTION);
                    
                        // Load Local Functions
                        this.ApplicationEngine.SetKernelMapElements(this.applicationAddressPoints.elements(new Integer(islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION)),islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION);
                    
                        
                    }
                    
                    /* Data is Labelled and Placement and Events Include Labels*/
                    if(this.applicationHasProcOps)
                    {
                        if(this.debug)
                        {
                            System.out.println("[INFO] processing Processor Operations for stack");
                        }
                        
                        // Load Downloaded File contents
                        this.ApplicationEngine.SetOperationMapElements(this.applicationOperationPoints.elements(new Integer(islandApplicationResource.FILE)),islandApplicationResource.FILE);
                        
                        // Load Kernel Cpu
                        this.ApplicationEngine.SetKernelMapElements(this.applicationTranslatorPoints.elements(new Integer(islandSoftwareEngine.KERNEL_CPU)),islandSoftwareEngine.KERNEL_CPU);
                    
                        // Load Program Arch info
                        this.ApplicationEngine.SetOperationMapElements(this.applicationOperationPoints.elements(new Integer(islandProcessorEngine.PROCESSOR_ARCHS)),islandProcessorEngine.PROCESSOR_ARCHS);
                    
                        // Load Program Counter Events
                        this.ApplicationEngine.SetOperationMapElements(this.applicationOperationPoints.elements(new Integer(islandProcessorEngine.PROCESSOR_PROGRAM_COUNTER_EVENT)),islandProcessorEngine.PROCESSOR_PROGRAM_COUNTER_EVENT);
                    
                        // Load Link Events
                        this.ApplicationEngine.SetOperationMapElements(this.applicationOperationPoints.elements(new Integer(islandProcessorEngine.PROCESSOR_LINK_EVENT)),islandProcessorEngine.PROCESSOR_LINK_EVENT);
                    
                        // Load Stack Events
                        this.ApplicationEngine.SetOperationMapElements(this.applicationOperationPoints.elements(new Integer(islandProcessorEngine.PROCESSOR_STACK_EVENT)),islandProcessorEngine.PROCESSOR_STACK_EVENT);
                        
                        // Load Processor Operations
                        this.ApplicationEngine.SetOperationMapElements(this.applicationOperationPoints.elements(new Integer(islandProcessorEngine.PROCESSOR_OPERATIONS)),islandProcessorEngine.PROCESSOR_OPERATIONS);
                    
                                            
                    }
                    
                    multiMapsLoaded = true;
                }
                catch(Exception Ex)
                {
                     updateDisplay("[ERROR] getting the stack assembled.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(Ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] problems getting the stack assembled:"+Ex.toString());
                        }
                }
                
                return multiMapsLoaded;
            }

            private boolean killApplication()
            {
                boolean appKilled = false;
                IslandSoftwareState = islandSoftwareEngine.APP_ENGINE_STOPPED;
                
                try
                {
                    if(savingApplicationStateToDisk)
                    {
                        this.ApplicationEngine.saveCurrentState();
                    }
                    
                    this.ApplicationEngine.stopEngine();
                
                    appKilled = true;
                }
                catch(Exception ex)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems killing Running Application:"+ex.toString());
                        ex.printStackTrace();
                    }
                }
                
                return appKilled;
            }
            
            private boolean startApplication()
            {
                boolean appStarted = false;
                IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_ENGINE_START;
                
                try
                {
                    if(savingApplicationStateToDisk)
                    {
                        this.ApplicationEngine.loadLastState();
                    }
                    
                    // Handle Inits
                    this.ApplicationEngine.startEngine();
                    
                    IslandSoftwareState = this.ApplicationEngine.GetEngineState();
                    
                    // do login if needed
                    if(this.ApplicationEngine.appRequiresRemoteLogin())
                    {
                        while(!this.ApplicationEngine.appLoginSuccessful())
                        {
                            
                                if(IslandTravelState != islandTravelAgent.POST)
                                {
                                    DOWNLOAD = this.ApplicationEngine.getAppLoginPage();
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] about to get the Login Page:"+DOWNLOAD);
                                        //updateDisplay("[INFO] about to get the Login Page:"+DOWNLOAD, DISPLAY_IN_TEXT);
                                        
                                   }
                                         
                                    // Get a lock
                                    synchronized(getLockObject())
                                    {                               
                                        IslandTravelState = islandTravelAgent.GET;
                                    
                                        // if we are not running islandtravel run it
                                        if(!_islandLinksIsRunning)
                                        {
                                            _islandLinksIsRunning = true;
                                            
                                            _islandConnectionManager = new IslandLinksConnectThread();
                                            _islandConnectionManager.start();
                                        }
                                        
                                        getLockObject().notifyAll();
                                                
                                        getLockObject().wait(180000);
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] current travel state:"+IslandTravelState);
                                        }
                                    }
                                }
                                
                                if(this.debug)
                                {
                                    System.out.println("[INFO] current travel state:"+IslandTravelState+"="+islandTravelAgent.WAIT_FOR_INPUT);
                                    System.out.println("[INFO] user download length:"+UserDownload.length()); 
                                    /* 
                                     synchronized(UiApplication.getEventLock())
                                        {   
                                            //_componentsManager.add(new RichTextField("[INFO] current travel state:"+IslandTravelState+"="+islandTravelAgent.WAIT_FOR_INPUT));
                                            //_componentsManager.add(new RichTextField("[INFO] user download length:"+UserDownload.length()));
                                        }
                                     */
                                }
                                        
                                if(UserDownload.length()>0)
                                {
                                    this.ApplicationEngine.setLoginPostContent(UserDownload);
        
                                    DOWNLOAD = this.ApplicationEngine.getAppLoginPage();
                                    UserData = this.ApplicationEngine.getAppLoginPostData();
                                            
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] about to post the Login Page:"+DOWNLOAD);
                                        System.out.println("[INFO] about to post the Login Data:"+UserData);
                                        /*
                                        synchronized(UiApplication.getEventLock())
                                        {   
                                            //_componentsManager.add(new RichTextField("[INFO] about to post the Login Page:"+DOWNLOAD));
                                            //_componentsManager.add(new RichTextField("[INFO] about to post the Login Data:"+UserData));
                                        }
                                        */
                                    }

                                    synchronized(getLockObject())
                                    {
                                        UserDownload = "";
                                        IslandTravelState = islandTravelAgent.POST;
                                        
                                        if(!_islandLinksIsRunning)
                                        {
                                            _islandLinksIsRunning = true;
                                            
                                            _islandConnectionManager = new IslandLinksConnectThread();
                                            _islandConnectionManager.start();
                                        }
                                        
                                        getLockObject().notifyAll();
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] notified everybody and about to wait");
                                            /*
                                            synchronized(UiApplication.getEventLock())
                                            {   
                                                //_componentsManager.add(new RichTextField("[INFO] notified everybody and about to wait"));
                                            }
                                            */
                                        }
                                        getLockObject().wait(180000);  
                                    
                                    } 
                                }
                                
                                if(this.debug)
                                {
                                    System.out.println("[INFO] current travel state:"+IslandTravelState+"="+islandTravelAgent.WAIT_FOR_INPUT);
                                    /*
                                    synchronized(UiApplication.getEventLock())
                                    {   
                                        _componentsManager.add(new RichTextField("[INFO] current travel state:"+IslandTravelState+"="+islandTravelAgent.WAIT_FOR_INPUT));
                                        _componentsManager.add(new RichTextField("[INFO] after post"));
                                    }
                                    */
                                }
                                
                                //synchronized(UiApplication.getEventLock())
                                {
                                    if(UserDownload.length()>0)
                                    {
                                        // Fire Loaded content event(s)
                                        this.ApplicationEngine.setLoginResultsPageContent(UserDownload);
                                    
                                        if(UserCookie!=null)
                                        {
                                            this.ApplicationEngine.setCurrentHttpCookie(UserCookie);
                                        }
                                    }
                                }
                                    
                                if(this.debug)
                                {
                                    System.out.println("[INFO] done with login process");
                                    updateDisplay("[INFO] done with login process",DISPLAY_IN_TEXT);
                                }
                                
                            
                        }
                    }
                    
                    // external operation needed?
                    if(this.debug)
                    {
                        System.out.println("[INFO] made it!");
                    }
                    
                    //this.ApplicationEngine.
                                            
                    appStarted = true;
                }
                catch(Exception ex)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems starting Loaded Application:"+ex.toString());
                        ex.printStackTrace();
                    }
                }
                
                return appStarted;
            }
            
            /**
             * <description>
             * @return <description>
             */
            private boolean allTranslatorPointsLoaded()
            {
                boolean pointsLoaded = false;
                String translatorPointsLocation = "";
                
                if((this.applicationTranslatorPoints != null)&&(applicationDetails !=null))
                {
                    translatorPointsLocation = (String) applicationDetails.get("TranslationLocation"); 
                
                    if((this.applicationTranslatorPoints.size()==0)||(!translatorPointsLocation.equals("")))
                    {
                        if(this.translatorPointsLocal())
                        {
                            this.loadLocalTranslatorPoints();
                        
                            pointsLoaded = true;
                        }
                        else
                        {
                            
                            // Sync and see if something needs doing
                            synchronized(getLockObject())
                            {
                                IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_TRANSLATION_POINTS;
                                
                                try 
                                {
                                    downloadTranslatorPoints();
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.APP_TRANSLATION_POINTS_READY)
                                    {
                                        
                                        if(savingTranslationsToDisk)
                                        {
                                            this.saveTranslatorPointsLocally();
                                        }
                                        
                                        pointsLoaded = true;
                                    }
                                }
                                catch(Exception ex)
                                {
                                    updateDisplay("[ERROR] getting Translation points from the Home Island.",DISPLAY_IN_TEXT);
                                    
                                    if(debug)
                                    {
                                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                                        System.out.println("[ERROR] problems getting Translation points from the Home Island:"+ex.toString());
                                        ex.printStackTrace();
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        pointsLoaded = true;
                    }
                }
                else
                {
                        if(this.translatorPointsLocal())
                        {
                            this.loadLocalTranslatorPoints();
                        
                            pointsLoaded = true;
                        }
                        else
                        {
                            // Sync and see if something needs doing
                            synchronized(getLockObject())
                            {
                                IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_TRANSLATION_POINTS;
                                
                                try 
                                {
                                    downloadTranslatorPoints();
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.APP_TRANSLATION_COMPLETED)
                                    {
                                        
                                        if(savingTranslationsToDisk)
                                        {
                                            this.saveTranslatorPointsLocally();
                                        }
                                        
                                        pointsLoaded = true;
                                    }
                                }
                                catch(Exception ex)
                                {
                                    updateDisplay("[ERROR] getting Translation points from the Home Island.",DISPLAY_IN_TEXT);
                                    
                                    if(debug)
                                    {
                                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                                        System.out.println("[ERROR] problems getting Translation points from the Home Island:"+ex.toString());
                                        ex.printStackTrace();
                                    }
                                }
                            }
                        }
                    }
                    
                return pointsLoaded;
            }
            
            /**
             * <description>
             * @return <description>
             */
            private boolean allAddressPointsLoaded()
            {
                boolean pointsLoaded = false;
                String addressPointsLocation = "";
                
                if((this.applicationAddressPoints != null)&&(applicationDetails !=null))
                {
                    addressPointsLocation = (String) applicationDetails.get("AddressesLocation"); 
                    
                    if((this.applicationAddressPoints.size()==0)||(!addressPointsLocation.equals("")))
                    {
                        if(this.addressPointsLocal())
                        {
                            this.loadLocalAddressPoints();
                        
                            pointsLoaded = true;
                        }
                        else
                        {
                            // Sync and see if something needs doing
                            synchronized(getLockObject())
                            {
                                IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_ADDRESS_POINTS;
                                
                                try 
                                {
                                    downloadAddressPoints();
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.APP_ADDRESS_POINTS_READY)
                                    {
                                        // update local resource Points
                                        this.applicationAddressPoints = applicationAddressValues;
                                    
                                        if(savingAddressesToDisk)
                                        {
                                            this.saveAddressPointsLocally();
                                        }
                                        
                                        pointsLoaded = true;
                                    }
                                }
                                catch(Exception ex)
                                {
                                    updateDisplay("[ERROR] getting Address points from the Home Island.",DISPLAY_IN_TEXT);
                                    
                                    if(debug)
                                    {
                                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                                        System.out.println("[ERROR] problems getting Address points from the Home Island:"+ex.toString());
                                        ex.printStackTrace();
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        pointsLoaded = true;
                    }
                }
                else
                {
                        if(this.addressPointsLocal())
                        {
                            this.loadLocalAddressPoints();
                        
                            pointsLoaded = true;
                        }
                        else
                        {
                            // Sync and see if something needs doing
                            synchronized(getLockObject())
                            {
                                IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_ADDRESS_POINTS;
                                
                                try 
                                {
                                    downloadAddressPoints();
                                    
                                    if(IslandSoftwareState == islandSoftwareEngine.APP_ADDRESS_POINTS_READY)
                                    {
                                        
                                        if(savingAddressesToDisk)
                                        {
                                            this.saveAddressPointsLocally();
                                        }
                                        
                                        pointsLoaded = true;
                                    }
                                }
                                catch(Exception ex)
                                {
                                    updateDisplay("[ERROR] getting Address points from the Home Island.",DISPLAY_IN_TEXT);
                                    
                                    if(debug)
                                    {
                                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                                        System.out.println("[ERROR] problems getting Address points from the Home Island:"+ex.toString());
                                        ex.printStackTrace();
                                    }
                                }
                            }
                        }
                    }
                
                
                return pointsLoaded;
            }

            /**
             * Are the translation points local? Check the 
             * location to see. If there is a file:/// 
             * in the string then probably so.
             * @return <description>
             */
            private boolean translatorPointsLocal()
            {
                boolean pointsLocal = false;
                
                if(this.translationLocation.indexOf("file://")>=0)
                {
                    pointsLocal = true;
                    this.appTranslationLocal = true;
                }
                
                if(this.translationLocation.indexOf("http://")>=0)
                {
                    this.appTranslationOnline = true;
                }
                
                if(this.translationLocation.indexOf("yatta://")>=0)
                {
                    this.appTranslationOnlinks = true;
                }
                
                return pointsLocal;
            }
            
            private boolean addressPointsLocal()
            {
                boolean pointsLocal = false;
                
                if(this.addressesLocation.indexOf("file://")>=0)
                {
                    pointsLocal = true;
                    this.appAddressesLocal = true;
                }
                
                if(this.addressesLocation.indexOf("http://")>=0)
                {
                    this.appAddressesOnline = true;
                }
                
                if(this.addressesLocation.indexOf("yatta://")>=0)
                {
                    this.appAddressesOnlinks = true;
                }
                
                return pointsLocal;
            }
            
            private boolean saveTranslatorPointsLocally()
            {
                boolean saved = false;
                
                return saved;
            }
            
            private boolean saveAddressPointsLocally()
            {
                boolean saved = false;
                
                return saved;
            }
            
            /**
             * Function loads the points from xml or wherever 
             * and puts them temporatily in memory,
             * but permanently as a resource and then saved  
             * if our profile/preferences permit.
             */
            private void loadLocalTranslatorPoints()
            {
                int translationPointIndx = 0;
                int mappingKeyIndx = 0;
                int mappingValueIndx = 0;
                int mappingKey = 0;
                String mappingValue = "";
                   
                try
                {         
                            /* load the points we just got 
                            while(translationPointIndx < UserMessage.length())
                            {
                                // Load em up in the applicationTranslationMapping
                                mappingKeyIndx = UserMessage.indexOf("=",translationPointIndx);
                                mappingValueIndx = UserMessage.indexOf(";",mappingKeyIndx);
                                
                                mappingKey = UserMessage.substring(translationPointIndx,mappingKeyIndx);
                                mappingValue = UserMessage.substring(mappingKeyIndx, mappingValueIndx);
                                
                                translationPointIndx = mappingValueIndx;
                                
                                applicationTranslationMapping.add(new Integer(mappingKey).intValue(), mappingValue);
                            }
                            */
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR] problems loading translator points (Locally).",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] problems loading translator points (Locally):"+ex.toString());
                            ex.printStackTrace();
                        }
                }
            }
            
            /**
             * Function loads the points from xml or memory map 
             * and puts them in kernel memory and then saved  
             * if our profile/preferences permit.
             */
            private void loadLocalAddressPoints()
            {
                int addressPointIndx = 0;
                int mappingKeyIndx = 0;
                int mappingValueIndx = 0;
                int mappingKey = 0;
                String mappingValue = "";
                   
                try
                {         
                            /* load the points we just got 
                            while(translationPointIndx < UserMessage.length())
                            {
                                // Load em up in the applicationTranslationMapping
                                mappingKeyIndx = UserMessage.indexOf("=",translationPointIndx);
                                mappingValueIndx = UserMessage.indexOf(";",mappingKeyIndx);
                                
                                mappingKey = UserMessage.substring(translationPointIndx,mappingKeyIndx);
                                mappingValue = UserMessage.substring(mappingKeyIndx, mappingValueIndx);
                                
                                translationPointIndx = mappingValueIndx;
                                
                                applicationTranslationMapping.add(new Integer(mappingKey).intValue(), mappingValue);
                            }
                            */
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR] problems loading address points (Locally).",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] problems loading address points (Locally):"+ex.toString());
                            ex.printStackTrace();
                        }
                }
            }
            
            private void downloadTranslatorPoints()
            {
                try
                {
                    while(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_TRANSLATION_POINTS)
                    {
                        if(!_homeIslandIsRunning)
                        {
                            // Start it
                            _homeIslandIsRunning = true;
                            _islandResourceManager = new HomeIslandThread();
                            _islandResourceManager.start();
                        }
                                
                        getLockObject().wait(150000);
                    }
                }
                catch(InterruptedException iex)
                {
                     updateDisplay("[ERROR] IEx - problems downloading translator points.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] IEx - problems downloading translator points:"+iex.toString());
                            iex.printStackTrace();
                        }
                }
                catch(ClassCastException ccex)
                {
                     updateDisplay("[ERROR]CCEx - problems downloading translator points.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ccex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] CCEx - problems downloading translator points:"+ccex.toString());
                            ccex.printStackTrace();
                        }
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR]problems downloading translator points.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] problems downloading translator points:"+ex.toString());
                            ex.printStackTrace();
                        }
                }
            }
            
            private void downloadAddressPoints()
            {
                try
                {
                    while(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_ADDRESS_POINTS)
                    {
                        if(!_homeIslandIsRunning)
                        {
                            // Start it
                            _homeIslandIsRunning = true;
                            _islandResourceManager = new HomeIslandThread();
                            _islandResourceManager.start();
                        }
                                
                        getLockObject().wait(150000);
                    }
                }
                catch(InterruptedException iex)
                {
                     updateDisplay("[ERROR] IEx - problems downloading address points.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] IEx - problems downloading address points:"+iex.toString());
                            iex.printStackTrace();
                        }
                }
                catch(ClassCastException ccex)
                {
                     updateDisplay("[ERROR]CCEx - problems downloading address points.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ccex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] CCEx - problems downloading address points:"+ccex.toString());
                            ccex.printStackTrace();
                        }
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR]problems downloading address points.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] problems downloading address points:"+ex.toString());
                            ex.printStackTrace();
                        }
                }
            }

            /**
             * Use islandConnection to get what details are needed, 
             * assuming that they aren't local or reachable by the 
             * current network.
             */
            private void getApplicationDetails()
            {
                // ask HomeIsland for details on this app
                
                synchronized(getLockObject())
                {
                    _applicationIsRunning = true;
                    IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_DETAILS;
                        
                    try 
                    {
                        while(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_DETAILS)
                        {
                            if(!_homeIslandIsRunning)
                            {
                                // Start it
                                _homeIslandIsRunning = true;
                                _islandResourceManager = new HomeIslandThread();
                                _islandResourceManager.start();
                            }
                            
                            // Wait on HomeIslandThread to return relevant details
                            getLockObject().wait();
                            //getLockObject().wait(180000);
                        }  
                    } 
                    catch ( InterruptedException ie ) 
                    {
                        updateDisplay("[ERROR] IEx - getting app details from the Home Island.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ie.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] IEx - getting app details from the Home Island:"+ie.toString());
                            ie.printStackTrace();
                        }
                    }
                    catch(Exception ex)
                    {
                        updateDisplay("[ERROR]getting app details from the Home Island.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] getting app details from the Home Island:"+ex.toString());
                            ex.printStackTrace();
                        }
                    }   
                }
                
                // Set the details you just got
                if(IslandSoftwareState == islandSoftwareEngine.APP_DETAILS_READY)
                {
                    try
                    {
                        // Set "usesAddresses" flag
                        if(applicationDetails.containsKey("UsesAddresses"))
                        {
                            this.applicationUsesAddresses = (boolean) ((Boolean)applicationDetails.get("UsesAddresses")).booleanValue();
                        }
                        
                        // Set "usesTranslator" flag
                        if(applicationDetails.containsKey("UsesTranslator"))
                        {
                            this.applicationUsesTranslator = (boolean)((Boolean)applicationDetails.get("UsesTranslator")).booleanValue();
                        
                           
                        }
                        
                        // Set "defines gui" flag
                        if(applicationDetails.containsKey("DefinesGui"))
                        {
                            this.applicationDefinesGui = (boolean)((Boolean)applicationDetails.get("DefinesGui")).booleanValue();
                        }
                        
                        // Set "hasResources" flag
                        if(applicationDetails.containsKey("HasResources"))
                        {
                            this.applicationHasResources = (boolean)((Boolean)applicationDetails.get("HasResources")).booleanValue();
                        }
                        
                        // Set "hasResources" flag
                        if(applicationDetails.containsKey("HasLayouts"))
                        {
                            this.applicationHasLayout = (boolean)((Boolean)applicationDetails.get("HasLayouts")).booleanValue();
                        }

                        // Set "hasProcOps" flag
                        if(applicationDetails.containsKey("HasProcOps"))
                        {
                            this.applicationHasProcOps = (boolean)((Boolean)applicationDetails.get("HasProcOps")).booleanValue();
                        }
                        
                        // Set "runs locally" flag
                        if(applicationDetails.containsKey("RunsOnLocal"))
                        {
                            this.applicationRunsLocal = (boolean)((Boolean)applicationDetails.get("RunsOnLocal")).booleanValue();
                        }
    
                        // Set "runs online"
                        if(applicationDetails.containsKey("RunsOnline"))
                        {
                            this.applicationRunsOnline = (boolean)((Boolean)applicationDetails.get("RunsOnline")).booleanValue();
                        } 
                        
                        // Set "runs online"
                        if(applicationDetails.containsKey("RunsOnlinks"))
                        {
                            this.applicationRunsOnlinks = (boolean)((Boolean)applicationDetails.get("RunsOnlinks")).booleanValue();
                        }

                        // Set "login needed"
                        if(applicationDetails.containsKey("NeedsAuthentication"))
                        {
                            this.applicationNeedsAuth = (boolean)((Boolean)applicationDetails.get("NeedsAuthentication")).booleanValue();
                        }
                        
                        
                        
                        
                        // create correct software engine
                        if(this.applicationRunsLocal&&this.applicationUsesTranslator)
                        {
                            if(this.applicationHasProcOps)
                            {
                                //this.ApplicationEngine = new islandSoftwareEngine(islandSoftwareEngine.TRANSLATION,islandSoftwareEngine.LOCAL, true);
                                this.ApplicationEngine  = new islandSoftwareEngine(islandSoftwareEngine.TRANSLATION,islandSoftwareEngine.LOCAL, _mainScreen.getHeight(), _mainScreen.getWidth(), true);
                            }
                            else
                            {
                                //this.ApplicationEngine = new islandSoftwareEngine(islandSoftwareEngine.TRANSLATION,islandSoftwareEngine.LOCAL);
                                this.ApplicationEngine  = new islandSoftwareEngine(islandSoftwareEngine.TRANSLATION,islandSoftwareEngine.LOCAL, _mainScreen.getHeight(), _mainScreen.getWidth());
                            }
                        }

                        // create correct software engine
                        if(this.applicationRunsOnline&&this.applicationUsesTranslator)
                        {
                            //this.ApplicationEngine = new islandSoftwareEngine(islandSoftwareEngine.TRANSLATION,islandSoftwareEngine.ONLINE);
                            this.ApplicationEngine  = new islandSoftwareEngine(islandSoftwareEngine.TRANSLATION,islandSoftwareEngine.ONLINE, _mainScreen.getHeight(), _mainScreen.getWidth());
                        }
                        
                        // create correct software engine
                        if(this.applicationRunsOnlinks&&this.applicationUsesTranslator)
                        {
                            //this.ApplicationEngine = new islandSoftwareEngine(islandSoftwareEngine.TRANSLATION,islandSoftwareEngine.ISLAND);
                            this.ApplicationEngine  = new islandSoftwareEngine(islandSoftwareEngine.TRANSLATION,islandSoftwareEngine.ISLAND, _mainScreen.getHeight(), _mainScreen.getWidth());
                        }
                       
                        

                        if(this.applicationNeedsAuth)
                        {
                            this.ApplicationEngine.appRequiresRemoteLogin(true);
                            
                            if((applicationDetails.containsKey("LoginName"))&&(applicationDetails.containsKey("LoginPass")))
                            {
                                this.ApplicationEngine.applicationUserNPass((String)applicationDetails.get("LoginName"),(String)applicationDetails.get("LoginPass"));
                            }
                        }
                        
                        if(this.applicationUsesTranslator)
                        {
                            // Where are the transator points
                            if(applicationDetails.containsKey("TranslationLocation"))
                            {
                                this.SetTranslationLocation((String)applicationDetails.get("TranslationLocation"));
                            }
                        
                            // load map point data
                            if(applicationDetails.containsKey("TranslationMapping"))
                            {
                                this.applicationTranslatorPoints = (MultiMap) applicationDetails.get("TranslationMapping");
                            }
                        }
                        
                        if(this.applicationHasProcOps)
                        {
                            // Where are the address points
                            if(applicationDetails.containsKey("OperationsLocation"))
                            {
                                this.SetOperationsLocation((String)applicationDetails.get("OperationsLocation"));
                            }
                            
                            // load map point data
                            if(applicationDetails.containsKey("OperationMapping"))
                            {
                                this.applicationOperationPoints = (MultiMap) applicationDetails.get("OperationMapping");
                            }
                        }
                    
                        if(this.applicationHasResources)
                        {
                            // Where are the address points
                            if(applicationDetails.containsKey("ResourcesLocation"))
                            {
                                this.SetResourcesLocation((String)applicationDetails.get("ResourcesLocation"));
                            }
                            
                            // load resource list data
                            if(applicationDetails.containsKey("ResourceList"))
                            {
                                this.applicationResourcePoints = (MultiMap) applicationDetails.get("ResourceList");
                            }
                        }
                    
                   
                        if(this.applicationUsesAddresses)
                        {
                        
                            // Where are the address points
                            if(applicationDetails.containsKey("AddressessLocation"))
                            {
                                this.SetAddressPointsLocation((String)applicationDetails.get("AddressesLocation"));
                            }
                        
                            // load resource list data
                            if(applicationDetails.containsKey("AddressList"))
                            {
                                this.applicationAddressPoints = (MultiMap) applicationDetails.get("AddressList");
                            }
                        }
                    }
                    catch(Exception ex)
                    {
                        if(this.debug)
                        {
                            System.out.println("[ERROR] problems setting Application Detail flags:"+ex.toString());
                        }
                    }
                }
            }
            
            private boolean allAppResourcesLoaded()
            {
                boolean allResourcesLoaded = false;
                
                if(this.applicationResourcePoints !=null)
                {
                    if(this.applicationResourcePoints.size()==0)
                    {
                        if(this.appResourcesLocal())
                        {
                            this.loadLocalResources();
                            
                            allResourcesLoaded = true;
                        }
                        else
                        {
                            try
                            {
                                IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_RESOURCES_DOWNLOAD;
                                
                                downloadResources();
                                
                                if(IslandSoftwareState == islandSoftwareEngine.APP_RESOURCES_DOWNLOADED)
                                {
                                    // update local resource Points
                                    this.applicationResourcePoints = applicationResourceValues;
                                    
                                    if(savingResourcesToDisk)
                                    {
                                        this.saveAppResourcesLocally();
                                    }
                                    
                                    allResourcesLoaded = true;
                                }
                            }
                            catch(Exception ex)
                            {
                                if(this.debug)
                                {
                                    System.out.println("[ERROR] problems downloading Resources for current aplication:"+ex.toString());
                                }
                            }
                        }
                    }
                }
                else
                {
                    if(this.appResourcesLocal())
                    {
                        this.loadLocalResources();
                            
                        allResourcesLoaded = true;
                    }
                    else
                    {
                            try
                            {
                                downloadResources();
                                
                                if(IslandSoftwareState == islandSoftwareEngine.APP_DOWNLOAD_COMPLETED)
                                {
                                    // update local resource Points
                                    this.applicationResourcePoints = applicationResourceValues;
                                    
                                    if(savingResourcesToDisk)
                                    {
                                        this.saveAppResourcesLocally();
                                    }
                                    
                                    allResourcesLoaded = true;
                                }
                            }
                            catch(Exception ex)
                            {
                                if(this.debug)
                                {
                                    System.out.println("[ERROR] problems downloading Resources for current aplication:"+ex.toString());
                                }
                            }
                        }
                    }
                    
                
                return allResourcesLoaded;
            }
            
            /**
             * Layouts are a part of the general translation
             * make sure that there is a grid and layout for
             * at least one page to flip the flag true
             * @return <description>
             */
            private boolean allAppLayoutsLoaded()
            {
                boolean allAppLayoutsLoaded = false;
                
                //if(allTranslatorPointsLoaded())
                {
                    
                    allAppLayoutsLoaded = true;
                }
                
                
                return allAppLayoutsLoaded;
            }
            

            private boolean allAppOperationsLoaded()
            {
                boolean allOperationsLoaded = false;
                String operationsLocation = "";
                
                if((this.applicationOperationPoints !=null)&&(applicationDetails !=null))
                {
                    operationsLocation = (String) applicationDetails.get("OperationsLocation"); 
                    
                    if((this.applicationOperationPoints.size()==0)||(!operationsLocation.equals("")))
                    {
                        if(this.appOperationsLocal())
                        {
                            this.loadLocalOperations();
                            
                            allOperationsLoaded = true;
                        }
                        else
                        {
                            try
                            {
                                IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_OPERATIONS_DOWNLOAD;
                                
                                downloadOperations();
                                
                                if(IslandSoftwareState == islandSoftwareEngine.APP_OPERATIONS_DOWNLOADED)
                                {
                                    // update local resource Points
                                    this.applicationOperationPoints = applicationOperationMapping;
                                    
                                    if(savingOperationsToDisk)
                                    {
                                        this.saveAppOperationsLocally();
                                    }
                                    
                                    allOperationsLoaded = true;
                                }
                            }
                            catch(Exception ex)
                            {
                                if(this.debug)
                                {
                                    System.out.println("[ERROR] problems downloading Operations for current aplication:"+ex.toString());
                                }
                            }
                        }
                    }
                    else
                    {
                        
                    }
                }
                else
                {
                    if(this.appOperationsLocal())
                    {
                        this.loadLocalOperations();
                            
                        allOperationsLoaded = true;
                    }
                    else
                    {
                            try
                            {
                                downloadOperations();
                                
                                if(IslandSoftwareState == islandSoftwareEngine.APP_OPERATIONS_DOWNLOADED)
                                {
                                    // update local operation Points
                                    //this.applicationOperationPoints = applicationOperationMapping;
                                    
                                    if(savingOperationsToDisk)
                                    {
                                        this.saveAppOperationsLocally();
                                    }
                                    
                                    allOperationsLoaded = true;
                                }
                            }
                            catch(Exception ex)
                            {
                                if(this.debug)
                                {
                                    System.out.println("[ERROR] problems downloading Operations for current aplication:"+ex.toString());
                                }
                            }
                        }
                    }
                    
                
                return allOperationsLoaded;
            }
            
            private boolean appResourcesLocal()
            {
                boolean localResources = false;
            
                if(this.resourcesLocation.indexOf("file://")>=0)
                {
                    localResources = true;
                    this.appResourcesLocal = true;
                }
                
                if(this.resourcesLocation.indexOf("http://")>=0)
                {
                    this.appResourcesOnline = true;
                }
                
                if(this.resourcesLocation.indexOf("yatta://")>=0)
                {
                    this.appResourcesOnlinks = true;
                }

                return localResources;
            }

            private boolean appOperationsLocal()
            {
                boolean localOperations = false;
            
                if(this.operationsLocation.indexOf("file://")>=0)
                {
                    localOperations = true;
                    this.appOperationsLocal = true;
                }
                
                if(this.operationsLocation.indexOf("http://")>=0)
                {
                    this.appOperationsOnline = true;
                }
                
                if(this.operationsLocation.indexOf("yatta://")>=0)
                {
                    this.appOperationsOnlinks = true;
                }

                return localOperations;
            }
            
            private boolean saveAppResourcesLocally()
            {
                boolean saved = false;
                
                return saved;
            }
             
            private boolean saveAppOperationsLocally()
            {
                boolean saved = false;
                
                return saved;
            }               
            
            /**
             * Function loads resources for the application in
             * 2 ways. One as a ';' separated list. And the Other 
             * is as an xml file with physical locations in tags.
             */
            private void loadLocalResources()
            {
                int translationPointIndx = 0;
                int mappingKeyIndx = 0;
                int mappingValueIndx = 0;
                int mappingKey = 0;
                String mappingValue = "";
                   
                try
                {         
                            /* load the points we just got 
                            while(translationPointIndx < UserMessage.length())
                            {
                                // Load em up in the applicationTranslationMapping
                                mappingKeyIndx = UserMessage.indexOf("=",translationPointIndx);
                                mappingValueIndx = UserMessage.indexOf(";",mappingKeyIndx);
                                
                                mappingKey = UserMessage.substring(translationPointIndx,mappingKeyIndx);
                                mappingValue = UserMessage.substring(mappingKeyIndx, mappingValueIndx);
                                
                                translationPointIndx = mappingValueIndx;
                                
                                applicationTranslationMapping.add(new Integer(mappingKey).intValue(), mappingValue);
                            }
                            */
                }
                catch(Exception ex)
                {
                    updateDisplay("[ERROR] getting app details from the Home Island:",DISPLAY_IN_TEXT);
                            
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] getting app details from the Home Island:"+ex.toString());
                        ex.printStackTrace();
                    }
                }
            }
            
            /**
             * Function loads operations for the application in
             * 2 ways. One as a ';' separated list. And the Other 
             * is as an xml file with physical locations in tags.
             */
            private void loadLocalOperations()
            {
                int translationPointIndx = 0;
                int mappingKeyIndx = 0;
                int mappingValueIndx = 0;
                int mappingKey = 0;
                String mappingValue = "";
                   
                try
                {         
                            /* load the points we just got 
                            while(translationPointIndx < UserMessage.length())
                            {
                                // Load em up in the applicationTranslationMapping
                                mappingKeyIndx = UserMessage.indexOf("=",translationPointIndx);
                                mappingValueIndx = UserMessage.indexOf(";",mappingKeyIndx);
                                
                                mappingKey = UserMessage.substring(translationPointIndx,mappingKeyIndx);
                                mappingValue = UserMessage.substring(mappingKeyIndx, mappingValueIndx);
                                
                                translationPointIndx = mappingValueIndx;
                                
                                applicationTranslationMapping.add(new Integer(mappingKey).intValue(), mappingValue);
                            }
                            */
                }
                catch(Exception ex)
                {
                    updateDisplay("[ERROR] getting app details from the Home Island:",DISPLAY_IN_TEXT);
                            
                    if(debug)
                    {
                        updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] getting app details from the Home Island:"+ex.toString());
                        ex.printStackTrace();
                    }
                }
            }
            
            private void downloadResources()
            {
                try
                {
                    IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_RESOURCES_DOWNLOAD;
                    
                    while(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_RESOURCES_DOWNLOAD)
                    {
                        if(!_homeIslandIsRunning)
                        {
                            // Start it
                            _homeIslandIsRunning = true;
                            _islandResourceManager = new HomeIslandThread();
                            _islandResourceManager.start();
                        }
                                
                        //sychronize first
                        synchronized(getLockObject())
                        { 
                            getLockObject().wait(150000);
                            //yield();
                        }
                    }
                }
                catch(InterruptedException iex)
                {
                     updateDisplay("[ERROR] IEx - downloading resources.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] IEx - downloading resources:"+iex.toString());
                            iex.printStackTrace();
                        }
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR] downloading Resources.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] downloading resources:"+ex.toString());
                            ex.printStackTrace();
                        }
                }
            }
            
            private void downloadOperations()
            {
                try
                {
                    IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_OPERATIONS_DOWNLOAD;
                    
                    while(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_OPERATIONS_DOWNLOAD)
                    {
                        if(!_homeIslandIsRunning)
                        {
                            // Start it
                            _homeIslandIsRunning = true;
                            _islandResourceManager = new HomeIslandThread();
                            _islandResourceManager.start();
                        }
                                
                        //sychronize first
                        synchronized(getLockObject())
                        { 
                            getLockObject().wait(150000);
                            //yield();
                        }
                    }
                }
                catch(InterruptedException iex)
                {
                     updateDisplay("[ERROR] IEx - downloading operations.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(iex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] IEx - downloading operations:"+iex.toString());
                            iex.printStackTrace();
                        }
                }
                catch(Exception ex)
                {
                     updateDisplay("[ERROR] downloading Operations.",DISPLAY_IN_TEXT);
                        
                        if(debug)
                        {
                            updateDisplay(ex.toString(),DISPLAY_IN_TEXT);
                            System.out.println("[ERROR] downloading operations:"+ex.toString());
                            ex.printStackTrace();
                        }
                }
            }
            
            private void showApplicationGui()
            {
                // Icon listing page?
                
                // show icons?
                
                // Actual Application page?
                
                // paint graphics stuffs
                updateDisplay("",DISPLAY_GRAPHIC_OBJECTS);
                
                // paint ui component stuffs
                updateDisplay("",DISPLAY_GUI_COMPONENTS);
               
            
                updateDisplay("",DISPLAY_GRID_OVERLAYS);
            }
            
            public void run()
            {
                try
                {
                    updateDisplay("Starting Application Engine",DISPLAY_IN_TEXT);
                    
                    // Load default Application
                    if(!_applicationLoaded)
                    {
                        _applicationLoaded = this.loadApplication();
                    }
                    
                    // Piece the Application gui together
                    if(guiAssembled)
                    {
                        showApplicationGui();
                    }
                    
                    // Start the Application
                    if(!loadedAppStarted)
                    {
                        loadedAppStarted = this.startApplication();
                    }
                    
                    mapsLoaded = true; 
            
                    
                    // Initialize State
                    if(!_applicationIsRunning)
                    {
                        _applicationIsRunning = true;
                        IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_USER_ACTION;
                    }
                        
                    // Main App Engine Thread Loop
                    while(IslandSoftwareState != islandSoftwareEngine.WAIT_FOR_ENGINE_STOP)
                    {
                        // Sync and see if something needs doing
                        synchronized(getLockObject())
                        {
                           
                            try 
                            {
                                // Nothing to do at the moment so wait a while
                                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_USER_ACTION)
                                {
                                    // waiting for Gui thread 
                                    getLockObject().wait(180000);
                                    join();
                                }    
                                
                                if(IslandSoftwareState == islandSoftwareEngine.KERNEL_NETWORK)
                                {
                                    
                                    DOWNLOAD = this.ApplicationEngine.getAppDownloadLocation();
                          
                                    if(this.debug)
                                    {
                                        System.out.println("Application Download Location is:"+DOWNLOAD);
                                    }
                                    
                                   // Get a lock
                                    //synchronized(getLockObject())
                                    {                               
                                        IslandTravelState = this.ApplicationEngine.getAppTravelMethod();
                                    
                                        // if we are not running islandtravel run it
                                        if(!_islandLinksIsRunning)
                                        {
                                            _islandLinksIsRunning = true;
                                            
                                            _islandConnectionManager = new IslandLinksConnectThread();
                                            _islandConnectionManager.start();
                                        }
                                        
                                        getLockObject().notifyAll();
                                                
                                        getLockObject().wait(180000);
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] current travel state:"+IslandTravelState);
                                        }
                                    }
                                    
                                    IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_ENGINE_OPERATION;
                                
                                }
                                
                                // must be a download or travel item
                                if((IslandTravelState  == islandTravelAgent.WAIT_FOR_INPUT)&&
                                    (IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_ENGINE_OPERATION)&&
                                    (ApplicationEngine.GetEngineState() == islandSoftwareEngine.KERNEL_NETWORK))
                                {
                                    
                                        synchronized(UiApplication.getEventLock())
                                        {
                                            // add new graphics and components firing content event
                                            this.ApplicationEngine.setDownloadResultsContent(UserDownload);
                                        
                                            //_componentsManager.setVerticalScroll(1);
                                            updateDisplay("",DISPLAY_GUI_COMPONENTS);
                                        }
                                        
                                        IslandTravelState = islandTravelAgent.DOWNLOADED;
                                        
                                        IslandSoftwareState = islandSoftwareEngine.WAIT_FOR_USER_ACTION;
                                        
                                        getLockObject().notifyAll();
                                        
                                        getLockObject().wait(180000);
                                    
                                        // catch everyone up
                                        //join();
                                }
                                
                                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_ENGINE_OPERATION)
                                {
                                    //getLockObject().wait(180000);
                                        
                                }
                                
                                if(IslandSoftwareState == islandSoftwareEngine.APP_ENGINE_STARTED)
                                {
                                    getLockObject().notifyAll();
                                    
                                    //getLockObject().wait(180000);
                                     if(this.debug)
                                    {
                                        updateDisplay("[INFO] started engine - application loaded",DISPLAY_IN_TEXT);
                                        System.out.println("[INFO] started engine & application loaded");
                                    }
                                    
                                    
                                    join();
                                    
                                    yield();
                                        
                                }
                                
                                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_ENGINE_START)
                                {
                                    //getLockObject().wait(180000);
                                    getLockObject().notifyAll();
                                        
                                    if(this.debug)
                                    {
                                        updateDisplay("[INFO] still starting engine - loading application",DISPLAY_IN_TEXT);
                                        System.out.println("[INFO] still starting engine & loading application");
                                    }
                                    
                                    //yield();
                                    join();
                                }
                                
                                if(IslandSoftwareState == islandSoftwareEngine.WAIT_FOR_USER_AWK)
                                {
                                    getLockObject().wait(1800000);
                                }
                            } 
                            catch ( InterruptedException ie ) 
                            {
                                updateDisplay("[ERROR] IEx - problems waiting for user event.",DISPLAY_IN_TEXT);
                                
                                if(debug)
                                {
                                    updateDisplay(ie.toString(),DISPLAY_IN_TEXT);
                                    System.out.println("[ERROR] IEx - problems waiting for user event:"+ie.toString());
                                }
                            }
                        }
                    }
                    
                    updateDisplay("Application engine Done!",DISPLAY_IN_TEXT);
                }
                catch(Exception e)
                {
                    updateDisplay("[ERROR]Something in the Application has gone horribly wrong ",DISPLAY_IN_TEXT);
                  
                    if(debug)
                    {
                        updateDisplay(e.toString(),DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems with something in the Application:"+e.toString());
                        e.printStackTrace();
                    }
                }
            }
        }
    
        /**
        * A private inner class to manage the socket.
        * <p>Not static since we access some methods in our parent.
        */
        private class IslandLinksConnectThread extends Thread
        {
            // Members --------------------------------------------------------------------------------------------------------
            
            private InputStreamReader _in;
            private OutputStreamWriter _out;
            private islandTravelAgent _ta;
            private StringBuffer tripStory;
            private DataBuffer tripSouvenier;
            private String tripInfo;
    
            private boolean debug;
            // Methods --------------------------------------------------------------------------------------------------------
            
            public IslandLinksConnectThread()
            {
                this.debug = true;
                //IslandTravelState = islandTravelAgent.WAIT_FOR_INPUT;
            }
            
            /**
            * Pass some data to the server and wait for a response.
            * @param data The data to send.
            */
            private void exchange(int destination, int travelAgent, int userCommand, String args) throws IOException
            {
                // create from islandtravel manager
                _ta = Tm.GetTravelAgent(travelAgent); 
                
                _ta.SetDebug(true);
                
                // go as islandLinks type   
                _ta.TravelIslandLinks(islandTravelAgent.PEER);
                    
                // using connection?      
                _ta.TravelViaConnection(true);
                
                // plan travel - setup headers we'll use
                _ta.PlanTravel();
                    
                try
                {
                    if(userCommand == islandTravelAgent.CONTACT)
                    {
                        _ta.GoingToProvider();
                    
                        // send initial commands or whatever 
                        _ta.BeginTravel();
                    }
                    else
                    {
                        switch(destination)
                        {
                                case islandTravelAgent.INTEGRATOR:
                                    _ta.GoingToIntegrator();
                                    break;
                                case islandTravelAgent.PROVIDER:
                                    _ta.GoingToProvider();
                                    break;
                                case islandTravelAgent.ULTRA_PEER:
                                    _ta.GoingToUltraPeer();
                                    break;
                                case islandTravelAgent.VENDOR:
                                    _ta.GoingToVendor();
                                    break;
                                case islandTravelAgent.PEER:
                                    _ta.GoingToPeer();
                                    break;
                                
                        }
                        
                        // setup new send commands
                        _ta.NewTrip(userCommand, args);
                    }
                
                    // travel -get data back
                    _ta.CompleteTrip(_out, _in);
                    
                    if(userCommand == islandTravelAgent.DONE)
                    {
                        // close this one so we can setup another
                        _ta.EndTravel();
                    }
                    else
                    {
                        tripStory = _ta.WhatHappened();
                        tripSouvenier = _ta.WhatYouGot();
                        tripInfo = _ta.WhatYouLearned();
                    }
                }
                catch(Exception ex)
                {
                    System.out.println("[ERROR] problems executing IslandConnection Exchange:"+ex.toString());
                }
                
                //updateDisplay(tripStory.toString(), DISPLAY_IN_TEXT);
            }
    
            private void download(int destination, int travelAgent, int resource, String userCommand, HttpConnection c)
            {
                // create from islandtravel manager
                _ta = Tm.GetTravelAgent(travelAgent); 
                
                _ta.SetDebug(true);
                
                // go as islandLinks type   
                _ta.TravelIslandLinks(islandTravelAgent.PEER);
                    
                // using connection?      
                _ta.TravelViaConnection(true);
                
                try
                {
                    if(travelAgent == islandTravelAgent.GET)
                    {
                        // plan travel - setup headers we'll use
                        _ta.PlanTravel();
                
                        //_ta.GoingToProvider();
                        _ta.GoingToPeer();
                    
                        // send initial commands or whatever 
                        ((islandTravelAgentHTTP)_ta).BeginTravel(c);
                    }
                    
                    if(travelAgent == islandTravelAgent.POST)
                    {
                        // plan travel - setup headers we'll use
                        ((islandTravelAgentHTTP)_ta).PlanTravel(islandTravelAgent.POST,"",islandResource.FILE,userCommand);
                        
                        _ta.GoingToPeer();
                
                        // send initial commands or whatever 
                        ((islandTravelAgentHTTP)_ta).BeginTravel(c);
                    }
                
                    if(travelAgent == islandTravelAgent.DOWNLOADED)
                    {
                    
                        // close this one
                        _ta.EndTravel();
                    }
                    else
                    {
                        // travel -get data back
                        _ta.CompleteTrip();
                    
                        tripStory = _ta.WhatHappened();
                        tripSouvenier = _ta.WhatYouGot();
                        tripInfo = _ta.WhatYouLearned();
                    }
                }
                catch(IOException ioEx)
                {
                    updateDisplay("[ERROR] I/O probalems talking to http server for download", DISPLAY_IN_TEXT);
                    
                    if(this.debug)
                    {
                        updateDisplay(ioEx.toString(), DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] I/O problems talking to http server for download:"+ioEx.toString());
                        ioEx.printStackTrace();
                    }
                }
                catch(Exception Ex)
                {
                    updateDisplay("[ERROR] probalems talking to http server for download", DISPLAY_IN_TEXT);
                    
                    if(this.debug)
                    {
                        updateDisplay(Ex.toString(), DISPLAY_IN_TEXT);
                        System.out.println("[ERROR] problems talking to http server for download:"+Ex.toString());
                        Ex.printStackTrace();
                    }
                }
            }
            
            /**
            * Implementation of Thread.
            */
            public void run()
            {
                StreamConnection connection = null;
                HttpConnection httpConnection = null;
                
                try
                {
                    if(IslandTravelState == islandTravelAgent.CONTACT)
                    {
                        updateDisplay("Opening Connection To Service(s) Provider...",DISPLAY_IN_TEXT);
                        connection = (StreamConnection)Connector.open(DEV_URL);
                        updateDisplay("Connection open",DISPLAY_IN_TEXT);
                        _in = new InputStreamReader(connection.openInputStream());
                        _out = new OutputStreamWriter(connection.openOutputStream());
                    
                    }

                    if((IslandTravelState == islandTravelAgent.GET)||(IslandTravelState == islandTravelAgent.POST))
                    {
                        updateDisplay("Opening Connection To Download...",DISPLAY_IN_TEXT);
                        httpConnection = (HttpConnection)Connector.open(DOWNLOAD+";deviceside=true");
                        
                        if(IslandTravelState == islandTravelAgent.GET)
                        {
                            httpConnection.setRequestMethod(HttpConnection.GET);
                        }
                        
                        if(IslandTravelState == islandTravelAgent.POST)
                        {
                            httpConnection.setRequestMethod(HttpConnection.POST);
                            httpConnection.setRequestProperty("Content-Length",(new Integer(UserData.length())).toString());
                            httpConnection.setRequestProperty("Referer","http://m.newsgator.com/Signon.aspx?ReturnUrl=%2fiPhone.aspx");
                            httpConnection.setRequestProperty("Content-Type","application/x-www-form-urlencoded");
                            httpConnection.setRequestProperty("Connection","keep-alive");
                            httpConnection.setRequestProperty("Keep-Alive","300");
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] DOWNLOAD location is :"+DOWNLOAD);
                        }
                        updateDisplay("Connection open",DISPLAY_IN_TEXT);
                    }
    
                    char[] input = new char[1024];
    
                    while((IslandTravelState!= islandTravelAgent.DONE)&&(IslandTravelState!= islandTravelAgent.DOWNLOADED)&&(IslandTravelState!= islandTravelAgent.POSTED))
                    {
                        if(IslandTravelState == islandTravelAgent.CONTACT)
                        {
                            // Send the CONTACT message.
                            exchange(islandTravelAgent.PROVIDER,islandTravelAgent.YATTA,islandTravelAgent.CONTACT,"");
        
                            synchronized(getLockObject())
                            {
                                _islandLinksIsRunning = true;
                                IslandTravelState = islandTravelAgent.WAIT_FOR_INPUT;
                        
                                try 
                                {
                                    UserMessage = this.tripStory.toString();
                                    
                                    while(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                    {
                                        
                                        // if this is not for an Application
                                        if(!_applicationIsRunning)
                                        {
                                            // Start the home Island
                                            if ( !_homeIslandIsRunning )
                                            {
                                                _homeIslandIsRunning = true;
                                                _islandResourceManager = new HomeIslandThread();
                                                _islandResourceManager.start();
                                            }
                                        }
                                        
                                        getLockObject().wait(180000);
                                       
                                    }
                                } 
                                catch ( InterruptedException ie ) 
                                {
                                    updateDisplay("destination timed out waiting for response.",DISPLAY_IN_TEXT);
                                }
                            }
                        }
                        
                        if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                        {
                            
                            synchronized(getLockObject())
                            {
                                       
                                IslandTravelState = islandTravelAgent.WAIT_FOR_INPUT;
                                        
                                if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                {
                                    getLockObject().notifyAll();
                                }
                            }
                        }
                        
                        if((IslandTravelState == islandTravelAgent.GET)||(IslandTravelState == islandTravelAgent.POST))
                        {
                            if(_ta !=null)
                            {
                                if((((islandTravelAgentHTTP)_ta).TripHasBegun())&&(!((islandTravelAgentHTTP)_ta).TripHasEnded()))
                                {
                                    try
                                    {
                                        // End the old trip
                                        this._ta.EndTravel();
                                    
                                        // ?
                                        ((islandTravelAgentHTTP)_ta).ResetTrip();
                                        
                                        // Kill old connection
                                        httpConnection.close();
                                        
                                        // Start New Connection for new download
                                        httpConnection = (HttpConnection)Connector.open(DOWNLOAD+";deviceside=true");
                                        if(IslandTravelState == islandTravelAgent.GET)
                                        {
                                            httpConnection.setRequestMethod(HttpConnection.GET);
                                            
                                            if(_islandApplicationManager != null)
                                            {
                                                if(_applicationIsRunning)
                                                {
                                                    String currentAppContentKey = _islandApplicationManager.ApplicationEngine.getCurrentLocationKey();
                                                    
                                                    if(currentAppContentKey != null)
                                                    {
                                                        if((currentAppContentKey.indexOf("m.newsgator.com[18]")>=0)||(currentAppContentKey.indexOf("m.newsgator.com[17]")>=0))
                                                        {
                                                            if(UserCookie!=null)
                                                            {
                                                                httpConnection.setRequestProperty("Cookie",UserCookie.substring(0,UserCookie.indexOf(";")));
                                                            }
                                                            else
                                                            {
                                                                httpConnection.setRequestProperty("Cookie",_islandApplicationManager.ApplicationEngine.getCurrentHttpCookie());
                                                            }
                                                            
                                                            httpConnection.setRequestProperty("Referer","http://m.newsgator.com/iPhone.aspx");
                                                            httpConnection.setRequestProperty("Connection","keep-alive");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        
                                        if(IslandTravelState == islandTravelAgent.POST)
                                        {
                                            httpConnection.setRequestMethod(HttpConnection.POST);
                                            httpConnection.setRequestProperty("Content-Length",(new Integer(UserData.length())).toString());
                                            httpConnection.setRequestProperty("Referer","http://m.newsgator.com/Signon.aspx?ReturnUrl=%2fiPhone.aspx");
                                            httpConnection.setRequestProperty("Content-Type","application/x-www-form-urlencoded");
                                            httpConnection.setRequestProperty("Connection","keep-alive");
                                            httpConnection.setRequestProperty("Keep-Alive","300");
                                        }
                                        
                                        if(this.debug)
                                        {
                                            if((DOWNLOAD !=null)&&(UserCookie!=null))
                                            {
                                                System.out.println("[INFO] Next DOWNLOAD location is :"+DOWNLOAD);
                                                System.out.println("[INFO] Cookie is :"+UserCookie.substring(0,UserCookie.indexOf(";")));
                                            }
                                        }
                                    }
                                    catch(Exception ex)
                                    {
                                        if(this.debug)
                                        {
                                            System.out.println("[ERROR] problems creating new download:"+ex.toString());
                                            ex.printStackTrace();
                                        }
                                    }
                                }
                            }
                            
                            UserDownload = "";
                            UserDataDownload = null;
                            //UserCookie = "";
                            
                            if(IslandTravelState == islandTravelAgent.GET)
                            {
                                // Download file
                                download(islandTravelAgent.PEER,IslandTravelState, islandResource.FILE, "",httpConnection);
                            }
                            else
                            {
                                /*
                                synchronized(UiApplication.getEventLock())
                                {
                                    //_componentsManager.add(new RichTextField("[INFO] before actual post"));
                                }
                                */
                                
                                // Download file
                                download(islandTravelAgent.PEER,IslandTravelState, islandResource.FILE, UserData,httpConnection);
                            
                                /*
                                synchronized(UiApplication.getEventLock())
                                {
                                    //_componentsManager.add(new RichTextField("[INFO] after post before 302"));
                                    //_componentsManager.add(new RichTextField("[INFO] before code"));
                                    //_componentsManager.add(new RichTextField("[INFO] Respose code :"+httpConnection.getResponseCode()));
                                    //_componentsManager.add(new RichTextField("[INFO] after code"));
                                }
                                */
                                
                                if((httpConnection.getResponseCode() == HttpConnection.HTTP_MOVED_PERM)||(httpConnection.getResponseCode() == HttpConnection.HTTP_MOVED_TEMP))
                                {
                                    /*
                                    synchronized(UiApplication.getEventLock())
                                    {
                                        //_componentsManager.add(new RichTextField("[INFO] in 302 logic"));
                                    }
                                    */
                                    
                                    String key = "";
                                    for(int a=0; (key = httpConnection.getHeaderFieldKey(a)) != null; a++)
                                    {
                                        if(this.debug)
                                        {
                                            System.out.println("header key:"+key+",value:"+httpConnection.getHeaderField(key));
                                        }
                                    
                                        if (key.equalsIgnoreCase("set-cookie"))
                                        {
                                            UserCookie = httpConnection.getHeaderField(key);
                                        
                                            if(this.debug)
                                            {
                                                System.out.println("New Cookie:"+UserCookie.substring(0,UserCookie.indexOf(";")));
                                      
                                                /*
                                                synchronized(UiApplication.getEventLock())
                                                {
                                                    _componentsManager.add(new RichTextField("[INFO]New Cookie:"+UserCookie.substring(0,UserCookie.indexOf(";"))));
                                                }
                                                */
                                            }
                                        }
                                    
                                        if (key.equalsIgnoreCase("location"))
                                        {
                                            DOWNLOAD = "http://"+ httpConnection.getHost() + httpConnection.getHeaderField("Location");
                                    
                                            if(this.debug)
                                            {
                                                System.out.println("New Location:"+DOWNLOAD);
                                            
                                                /*
                                                synchronized(UiApplication.getEventLock())
                                                {
                                                    _componentsManager.add(new RichTextField("[INFO]New Location:"+DOWNLOAD));
                                                }
                                                */
                                            }
                                        }
                                    }
                    
                                    ((islandTravelAgentHTTP)_ta).ResetTrip();
                                        
                                    // Kill old connection
                                    httpConnection.close();
                                     
                                    /*
                                    synchronized(UiApplication.getEventLock())
                                    {
                                        _componentsManager.add(new RichTextField("[INFO] after close"));
                                    }
                                    */
                                      
                                    // Start New Connection for new download
                                    httpConnection = (HttpConnection)Connector.open(DOWNLOAD+";deviceside=true");
                                    
                                    /*
                                    synchronized(UiApplication.getEventLock())
                                    {
                                        _componentsManager.add(new RichTextField("[INFO] after open"));
                                    }
                                    */
                                    
                                    IslandTravelState = islandTravelAgent.GET;
                                    
                                    httpConnection.setRequestMethod(HttpConnection.GET);
                                    httpConnection.setRequestProperty("Cookie",UserCookie.substring(0,UserCookie.indexOf(";")));
                                    httpConnection.setRequestProperty("Referer","http://m.newsgator.com/Signon.aspx?ReturnUrl=%2fiPhone.aspx");
                                    httpConnection.setRequestProperty("Connection","keep-alive");
                                    httpConnection.setRequestProperty("Keep-Alive","300");
                                    
                                    /*
                                    synchronized(UiApplication.getEventLock())
                                    {
                                        _componentsManager.add(new RichTextField("[INFO] before actual content download"));
                                    }
                                    */
                                    
                                    // Download file
                                    download(islandTravelAgent.PEER,IslandTravelState, islandResource.FILE, "",httpConnection);
                                
                                    /*
                                    synchronized(UiApplication.getEventLock())
                                    {
                                        _componentsManager.add(new RichTextField("[INFO] RSS download Done!"));
                                    }
                                    */
                                }    
                            }
                            
                            
                            // Data in 3 formats
                            UserDownload = this.tripStory.toString();
                            UserDataDownload = this.tripSouvenier;
                            if(this.tripInfo!=null)
                            {
                                UserCookie = this.tripInfo;
                            }
                            
                            try
                            {
                                
                                if(this.debug)
                                {
                                    /*
                                    if(UserDataDownload!=null)
                                    {
                                        if(UserDataDownload.getArrayLength()>0)
                                        {
                                            synchronized(UiApplication.getEventLock())
                                            {
                                                _componentsManager.add(new RichTextField("[INFO] User Data donload has data!"));
                                            }
                                        }
                                    }
                                    */
                                    /*
                                    if(UserDownload!=null)
                                    {
                                        if(UserDownload.length()>0)
                                        {
                                            synchronized(UiApplication.getEventLock())
                                            {
                                                _componentsManager.add(new RichTextField("[INFO] UserDownload has Data!"));
                                            }
                                        }
                                    }
                                    */
                                    
                                    /*
                                    if(UserCookie!=null)
                                    {
                                        if(UserCookie.length()>0)
                                        {
                                            synchronized(UiApplication.getEventLock())
                                            {
                                                _componentsManager.add(new RichTextField("[INFO] User Cookie is!"+UserCookie));
                                            }
                                        }
                                    }
                                    */
                                }
                                    
                                if(UserDownload.length()>0)
                                {
                                        synchronized(getLockObject())
                                        {
                                       
                                            IslandTravelState = islandTravelAgent.WAIT_FOR_INPUT;
                                        
                                            if(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                            {
                                             
                                                getLockObject().notifyAll();
                                                
                                                getLockObject().wait(180000);
                                            }
                                        }
                                 }
                                 else
                                 {
                                        //IslandTravelState = islandTravelAgent.WAIT_FOR_DOWNLOAD;
                                    IslandTravelState = islandTravelAgent.DOWNLOADED;
                                 }
                            }
                            catch(Exception ex)
                            {
                                if(this.debug)
                                {
                                    System.out.println("[ERROR] completed GET:"+ex.toString());
                                    ex.printStackTrace();
                                }
                             }
                        }
                
                        if(IslandTravelState == islandTravelAgent.WAIT_FOR_OUTPUT)
                        {
                            // send commands .
                            exchange(islandTravelAgent.PROVIDER,islandTravelAgent.YATTA,islandTravelAgent.SEND_COMAND,UserCommands);
        
                            synchronized(getLockObject())
                            {
                                _islandLinksIsRunning = true;
                                IslandTravelState = islandTravelAgent.WAIT_FOR_INPUT;
                        
                                try 
                                {
                                    while(IslandTravelState == islandTravelAgent.WAIT_FOR_INPUT)
                                    {
                                        // if this is not for an Application
                                        if(!_applicationIsRunning)
                                        {
                                            // Start the home Island
                                            if ( !_homeIslandIsRunning )
                                            {
                                                _homeIslandIsRunning = true;
                                                _islandResourceManager = new HomeIslandThread();
                                                _islandResourceManager.start();
                                            }
                                        }
                                        
                                        getLockObject().wait(180000);  
                                    }
                                } 
                                catch ( InterruptedException ie ) 
                                {
                                    updateDisplay("destination timed out waiting for response.",DISPLAY_IN_TEXT);
                                    
                                }
                            }
                        }
                    
                        if(IslandTravelState == islandTravelAgent.DONE)
                        {
                            // Send the DONE message.
                            exchange(islandTravelAgent.VENDOR,islandTravelAgent.YATTA, islandTravelAgent.DONE,"");
        
                            // Close the current connection.
                            _in.close();
                            _out.close();
        
                            synchronized(getLockObject())
                            {
                                _islandLinksIsRunning = false;
                                IslandTravelState = islandTravelAgent.DONE;
                            
                                try 
                                {
                                    getLockObject().notify();
                                } 
                                catch ( Exception ie ) 
                                {
                                    updateDisplay("Done.", DISPLAY_IN_TEXT);
                                }
                            }
                        }

                        if(IslandTravelState == islandTravelAgent.DOWNLOADED)
                        {
                            // clean up download,...
                            download(islandTravelAgent.PEER,IslandTravelState, islandResource.FILE, "",httpConnection);
                            
                            // end everything else,...
                            updateDisplay("Closing Connection To Web.",DISPLAY_IN_TEXT);
                            httpConnection.close();
                            updateDisplay("Connection Closed.", DISPLAY_IN_TEXT);
                            
                            synchronized(getLockObject())
                            {
                                _islandLinksIsRunning = false;
                                IslandTravelState = islandTravelAgent.DOWNLOADED;
                            
                                try 
                                {
                                    // let everyone know we're dyin off
                                    getLockObject().notifyAll();
                                    //yield();
                                } 
                                catch ( Exception ie ) 
                                {
                                    updateDisplay("Items(s) Downloaded", DISPLAY_IN_TEXT);
                                }
                            }
                        }
                    }
                }
                catch(IOException e)
                {
                    System.err.println(e.toString());
                    updateDisplay(e.toString(),DISPLAY_IN_TEXT);
                    e.printStackTrace();
                    
                }
            }
        }
    }
}
 

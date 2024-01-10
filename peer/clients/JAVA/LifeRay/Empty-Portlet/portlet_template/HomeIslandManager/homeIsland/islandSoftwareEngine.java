/*
 * islandSoftwareEngine.java
 *
 * � EKenDo, LLC , 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.engine;

// base java
import java.util.*;
// standard j2me
import javax.microedition.lcdui.*;
// rim-system
import net.rim.device.api.system.Bitmap;
import net.rim.device.api.system.EncodedImage;
// rim-ui
import net.rim.device.api.ui.*;
import net.rim.device.api.ui.container.*;
import net.rim.device.api.ui.component.*;
// rim-util
import net.rim.device.api.util.*;
// base ekendo
import com.ekendotech.homeIsland.resources.*;
import com.ekendotech.homeIsland.travel.*;
import com.ekendotech.homeIsland.engine.*;

/**
 * This class will perform tasks specific to 
 * island software whether the application runs
 * from local resources, online resources or
 * island resources. This class knows how to use a
 * translation mapping, remote accesa, a cloud, grid 
 * and networked resources to run software in this environment.
 */
public class islandSoftwareEngine 
{
    // class flags
    private boolean debug;
    
    // device metrics
    private int displayWidth;
    private int displayHeight;
    
    // Instatiated memory data
    private MultiMap translatorPoints; // kernel calls, content management and events
    private Hashtable cloudDetails; // cloud access info
    private Hashtable remoteAccessPoints; // remote locations and types key:location
    private Hashtable resourcePoints; // resource locations and types
    
    private MultiMap guiMap; // main gui elements and events
    private MultiMap layoutMap; // main layout elements and events
    private MultiMap kernelMap; // Address index, Network locations, function listing, api listing, and events
    
    // Static objects
    private static Vector seGrids = new Vector(); // GridScreenDetails w/ Screen Names inside
    private static Vector seScreens = new Vector(); // String Screens inside 
    private static Vector seLayouts = new Vector(); // DisplayLayoutDetails inside
    private static Vector seComponents = new Vector(); // GraphicGuiDetails inside
    private static Vector seGraphics = new Vector(); // GraphicGuiDetails inside
    
    // Content management
    private static Vector seContent = new Vector(); // ContentManagementDetails inside
    
    // Only One Kernel
    private static KernelExecutionDetails seKernel = new KernelExecutionDetails();
    
    // types
    private int engineType;
    private int softwareType;
    private int currentStatus; // running, downloading etc
    private int currentState; // what state are we in network, gui
    private int currentKernelStatus;
    
     // Gui boolean flags
    private boolean _hasLayouts;
    private boolean _hasGraphics;
    private boolean _hasComponents;
    private boolean _hasStaticAddresses;
    private boolean _hasProcOperations;
    private boolean _needsRemoteLogin;
    private boolean _loggedInSuccessfully;
    private boolean _needsExternalEvent;
    private boolean _eventFiredSuccessfully;
    
    // Logic boolean flags
    private boolean _usesVirtualStack;
    private boolean _addressMapInitialized;
    private boolean _processorEngineStarted;
    
    // Gui Containers
    private Manager graphicsContainer;
    private Manager componentsContainer;
    
    // App Credentials, etc
    private String user;
    private String pass;
    private String loginContentLocation;
    private String loginLocationKey;
    private String loginTravelMethod;
    private String loginManagementKey;
    private String loginPostContent;
    private String loginResultContent;
    
    // App external fullfillment
    private String downloadContentLocation;
    private String downloadLocationKey;
    private String downloadTravelMethod;
    private String downloadManagementKey;
    private String downloadPostContent;
    private String downloadResultContent;
    
    // App internal state
    private String currentLocationKey;
    private String currentHttpCookie;
    private String currentClickedObj;

    // Public classes
    public class ContentManagementDetails
    {
        public String Id;
        public String Content;
        public String Name;
        public String Event;
        public String Operation;
        public String Item;
        public Stack Events;
        public Hashtable FullfillmentDetails;
        public String Regex;
        public Stack Regexes;
        
        public ContentManagementDetails()
        {
            this.Id = "";
            this.Content = "";
            this.Name = "";
            this.Event = "";
            this.Operation = "";
            this.Item = "";
            this.Regex = ""; 
            this.Events = null;
            this.Regexes = null;
            this.FullfillmentDetails = null;
        }
        
        public void SetEventDetails()
        {
            try
            {
                if(Name.indexOf("-")>=0)
                {
                    // do what 
                    this.Operation = Name.substring(0,Name.indexOf("-"));
                            
                    if(Name.indexOf("_")>=0)
                    {
                        //to what
                        this.Item = Name.substring(Name.indexOf("-")+1,Name.indexOf("_"));
                    }
                    else
                    {
                        //to what
                        this.Item = Name.substring(Name.indexOf("-")+1);
                    }
                }
                    
                if(Event.indexOf("Loaded")>=0)
                {
                    // add Event to collection
                    if(this.Events == null)
                    {
                        this.Events = new Stack();
                    }
                    
                    this.Events.addElement("Loaded");
                    
                    if(Event.indexOf(":")>=0)
                    {
                        if(Event.indexOf("_")>=0)
                        {
                            // save content location
                            this.Content = Event.substring(Event.indexOf("_")+1,Event.indexOf(":"));
                        }
                        else
                        {
                            // save content location/tag
                            this.Content = Event.substring(0,Event.indexOf(":"));
                        }
                    }
                }
                
                if((Event.indexOf("Draw")>=0)&&(Event.indexOf("Precise")>=0))
                {
                    // add Event to collection
                    if(this.Events == null)
                    {
                        this.Events = new Stack();
                    }
                    
                    this.Events.addElement("Draw_Precise");
                }
            }
            catch(Exception ex)
            {
                System.out.println("[ERROR] problems setting event details:"+ex.toString());
            }
        }
        
        /**
         * Function sets Regex list if required
         * or Fullfillment list if required
         */
        public void SetManagementDetails(String key)
        {
            String action = "";
            String regex = "";
            String item = ""; 
            
            try
            {
                if(FullfillmentDetails == null)
                {
                    FullfillmentDetails = new Hashtable();
                }
                
                if(key.indexOf("_")>=0)
                {
                    if(key.indexOf(":")>=0)
                    {
                        action = key.substring(key.indexOf("_")+1,key.indexOf(":"));
                        item = key.substring(key.indexOf(":")+1); 
                    }
                }
                
                if((action.length()>0)&&(item.length()>0))
                {
                    FullfillmentDetails.put(action,item);
                }               
                
                if(action.indexOf("Regex")>=0)
                {
                    Regex = item;
                    
                    if(Regexes == null)
                    {
                        Regexes = new Stack();
                    }
                    
                    Regexes.addElement(item);
                }

                if(Name.indexOf("-")>=0)
                {
                    // do what 
                    this.Operation = Name.substring(0,Name.indexOf("-"));
                        
                    //to what
                    if(Name.indexOf("_")>=0)
                    {
                        //to what
                        this.Item = Name.substring(Name.indexOf("-")+1,Name.indexOf("_"));
                    }
                    else
                    {
                        //to what
                        this.Item = Name.substring(Name.indexOf("-")+1);
                    }
                }
            }
            catch(Exception ex)
            {
                System.out.println("[ERROR] problems trying to set management details:"+ex.toString());
            }
        }
        
        /**
         * Function runs regexes
         * @param c <description>
         * @return <description>
         */
        public boolean ManageContent(String c)
        {
            return false;
        }
        
        /**
         * Function sets component values
         * based on fullfillment rules
         * @param f <description>
         * @return <description>
         */
        public boolean ManageComponent(Field f, int i)
        {
            return false;
        }
        
        /**
         * Function sets graphic values
         * based on fullfillment rules
         * @param g <description>
         * @return <description>
         */
        public boolean ManageGraphics(Object g,int  i)
        {
            return false;
        }
    }
    
    /**
     * 
     */
    public static class KernelExecutionDetails
    {
        public String Id; // profile or mapped item id
        public String Lib;
        public String Api; // api-framework symbol
        public String Function; // cpu, kernel or api function
        public String Location; // network location
        public String Address; // memory place holder for callstack
        public String ProgramOffset;
        public String Name; // profile or mapped item name
        public String Vendor;
        public Object Value;
        
        public Vector Locations;  // where you're going
        public Vector Travelled; // where you've been so far
        public Vector Apis; // apis for this app
        public Vector Libs; // libraries for the apis for this app
        
        public Hashtable Addresses; // wireframe of pointers
        public Hashtable CStringAddresses;
        public Hashtable FunctionAddresses; // functions for this app
        public Hashtable LabelledValues;
        
        public Hashtable VendorLibs; // vendor key to lib value
        public Hashtable ApiLibs; // api key to vector list of other api/libs
        public Hashtable ApiFunctions; //api key to vector list of functions
        public Hashtable LibFunctions; //lib key to vector list of functions
        
        public Hashtable MappedFunctions; // Our own function replacements ala Wine
        public Hashtable MappedApis;
        public Hashtable MappedLibs;
        public Hashtable MappedCpus; 
        
        public islandProcessorEngine Cpu;
 
        public boolean StaticAddresses;
        public boolean ProgramFunctions;
        public boolean ProgramAPIs;
        public boolean ProgramLibs;
        public boolean CpuInitialized; 
        
        public int State;
        
        public KernelExecutionDetails()
        {
            this.Id = "";
            this.Api = "";
            this.Lib = "";
            this.Function = "";
            this.Location = "";
            this.Address = "";
            this.Name = "";
            
            this.Locations = null; // full network addresses
            this.Travelled = null; // network addresses that have already been 'touched'
            
            this.Addresses = null; // ht with address as keys and Function values as values  
            this.CStringAddresses = null; // resolvable to a value
            this.FunctionAddresses = null; // ht with types as keys {API, LOCAL, KERNEL}
            this.LabelledValues = null;
            
            this.MappedFunctions = null; // functions that we replaced with orig function as key
            this.MappedApis = null; // apis that we replaced with orig api as key
            this.MappedLibs = null;
            this.MappedCpus = null;
            
            this.Apis = null; // full name frameworks
            
            this.Cpu = null;
            
            this.StaticAddresses = false;
            this.CpuInitialized = false;
            this.ProgramFunctions = false;
        }
        
        public boolean SetUpCpu(int arch)
        {
            boolean cpuSetUp = false;
            
            if(this.Cpu == null)
            {
                if(this.Addresses.containsKey("Start"))
                {
                    this.Cpu = new islandProcessorEngine(arch,false,(String)this.Addresses.get("Start"));
                }
                else
                {
                    this.Cpu = new islandProcessorEngine(arch);
                }
                
                if(this.Addresses.containsKey("Offset"))
                {
                    this.Cpu.SetProgramOffset((String)this.Addresses.get("Offset"));
                }
                
                cpuSetUp = true;
                CpuInitialized = true;
            }
            
            return cpuSetUp;
        }

        public boolean SetUpCpu(int arch, String startAddress)
        {
            boolean cpuSetUp = false;
            
            if(this.Cpu == null)
            {
                this.Cpu = new islandProcessorEngine(arch, false, startAddress);
                
                if(this.Addresses == null)
                {
                    this.Addresses = new Hashtable();
                }
                
                this.Addresses.put("Start",startAddress);
                
                if(this.Addresses.containsKey("Offset"))
                {
                    this.Cpu.SetProgramOffset((String)this.Addresses.get("Offset"));
                }
                
                CpuInitialized = true;
                cpuSetUp = true;
            }
            
            return cpuSetUp;
        }
        
        public boolean SetUpCpu(int arch, boolean usesMMu)
        {
            boolean cpuSetUp = false;
            String startAddress = "";
            
            if(this.Cpu == null)
            {
                if(this.Addresses.containsKey("Start"))
                {
                    this.Cpu = new islandProcessorEngine(arch,usesMMu,(String)this.Addresses.get("Start"));
                }
                else
                {                    
                    this.Cpu = new islandProcessorEngine(arch,usesMMu);
                }
                
                if(this.Addresses.containsKey("Offset"))
                {
                    this.Cpu.SetProgramOffset((String)this.Addresses.get("Offset"));
                }   
            
                cpuSetUp = true;
                CpuInitialized = true;
            }
            
            return cpuSetUp;
        }
        
        public boolean SetUpCpu(int arch, boolean usesMMu, String startAddress)
        {
            boolean cpuSetUp = false;
            
            if(this.Cpu == null)
            {
                this.Cpu = new islandProcessorEngine(arch,usesMMu);
                
                if(this.Addresses == null)
                {
                    this.Addresses = new Hashtable();
                }
                
                this.Addresses.put("Start",startAddress);
                
                if(this.Addresses.containsKey("Offset"))
                {
                    this.Cpu.SetProgramOffset((String)this.Addresses.get("Offset"));
                }
                
                cpuSetUp = true;
                CpuInitialized = true;
            }
            
            return cpuSetUp;
        }
        
        public boolean SetUpCpu(islandProcessorEngine.MachineConfig mc)
        {
            boolean cpuSetUp = false;
            
            if(this.Cpu == null)
            {
                this.Cpu = new islandProcessorEngine(mc);
            }
            
            cpuSetUp = true;
            CpuInitialized = true;
            
            
            if(this.Addresses.containsKey("Offset"))
            {
                this.Cpu.SetProgramOffset((String)this.Addresses.get("Offset"));
            }
            
            return cpuSetUp;
        }
        
        public boolean CpuAlreadySetUp()
        {
            return CpuInitialized;
        }
        
        public boolean AddCpuAddress(String operationAddress,String operationInstruction, int procType)
        {
            boolean cpuAddressAdded = false;
            
            switch(procType)
            {
                case islandProcessorEngine.PROCESSOR_ARM_ARCH:
                    
                    if(seKernel.Cpu !=null)
                    {
                        // set up the Machine Config if it is null or incomplete
                        if(seKernel.Cpu.GetMachineStatus() != islandProcessorEngine.PROC_MACH_INITIALIZED)
                        {
                            seKernel.Cpu.InitMachine(false,true, procType);
                            seKernel.CpuInitialized = true;
                        }
                        else
                        {
                            seKernel.CpuInitialized = true;
                        }
                        
                        if(seKernel.MappedCpus == null)
                        {
                            seKernel.MappedCpus = new Hashtable();
                        }
                        
                        if(!seKernel.MappedCpus.contains(new Integer(procType)))
                        {
                            seKernel.MappedCpus.put(seKernel.Cpu.GetMachineName(),new Integer(procType));
                        }
                        
                        // add operation to Cpu's SetInstructionMapElement
                        seKernel.Cpu.SetProgramInstruction(operationAddress,operationInstruction);
                        
                        cpuAddressAdded = true;
                    }
                    
                    break;
            }
            
            return cpuAddressAdded;
        }
        
        public boolean AddFunctionMapping(String origFunc, String mappedFunc)
        {
            return false;
        }
        
        public boolean AddFunctionAddress(int type)
        {
            boolean functionLocationAdded = false;
            
            switch(type)
            {
                case islandSoftwareEngine.APP_API_FUNCTION:
                    try
                    {
                        if(this.FunctionAddresses == null)
                        {
                            this.FunctionAddresses = new Hashtable();
                        }
                        
                        this.FunctionAddresses.put(this.Function,this.Address);
                        functionLocationAdded = true;
                    }
                    catch(Exception exc)
                    {
                        System.out.println("[ERROR] problems adding api function address:"+exc.toString());
                    }
                    break;
                case islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION:
                    try
                    {
                        if(this.FunctionAddresses == null)
                        {
                            this.FunctionAddresses = new Hashtable();
                        }
                        
                        this.FunctionAddresses.put(this.Function,this.Address);
                        functionLocationAdded = true;
                    }
                    catch(Exception exc)
                    {
                        System.out.println("[ERROR] problems adding local function address:"+exc.toString());
                    }
                    break;
            }
            
            return functionLocationAdded;
        }
        
        public boolean AddFrameworkMapping(String origApi, String mappedApi)
        {
            return false;
        }
        
        public boolean AddFramework(String apiName)
        {
            boolean added = false;
            
            if(this.Apis==null)
            {
                this.Apis = new Vector();
            }
            
            this.Apis.addElement(apiName);
            
            added = true;
            
            return added;
        }
        
        public boolean AddVendorLibrary(String vendorKey, String libName)
        {
            boolean libAdded = false;
            
            try
            {
                if(this.VendorLibs == null)
                {
                    this.VendorLibs = new Hashtable();
                }
                        
                this.VendorLibs.put(vendorKey,libName);
                libAdded = true;
            }
            catch(Exception exc)
            {
                System.out.println("[ERROR] problems adding vendor laibrary:"+exc.toString());
            }
            
            return libAdded;
        }
        
        public boolean SetProgramAddress(String locationName, String appAddress, int locationType)
        {
            boolean programAddressAdded = false;
            
            switch(locationType)
            {   case islandSoftwareEngine.APP_STATIC_ADDRESS:
                    programAddressAdded = true;
                    break;
                case islandSoftwareEngine.KERNEL_MEMORY:
                    if(seKernel.Addresses == null)
                    {
                        seKernel.Addresses = new Hashtable();
                    }
                    
                    seKernel.Addresses.put(locationName, appAddress);
                        
                    programAddressAdded = true;
                    break;
                case islandProcessorEngine.PROCESSOR_KERNEL_FUNCTION:
                    programAddressAdded = true;
                    break;
                case islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION:
                    programAddressAdded = true;
                    break;
                case islandSoftwareEngine.APP_API_FUNCTION:
                    programAddressAdded = true;
                    break;
                case islandSoftwareEngine.APP_API_FRAMEWORK:
                    programAddressAdded = true;
                    break;
            }
            
            return programAddressAdded;
        }
        
        public String GetProgramAddress(String variable, String locationName, int locationType)
        {
            String programAddress = "";;
            
            switch(locationType)
            {
                case islandSoftwareEngine.KERNEL_MEMORY:
                    if(seKernel.Addresses == null)
                    {
                        seKernel.Addresses = new Hashtable();
                    }
                    else
                    {
                        programAddress = (String)seKernel.Addresses.get(locationName);
                    }
                    
                    // save address and it's current value
                    if(!programAddress.equals(""))
                    {
                        if(seKernel.LabelledValues.containsKey(programAddress))
                        {
                            String value = (String)seKernel.LabelledValues.get(programAddress);
                            seKernel.LabelledValues.put(variable,value);
                            seKernel.Addresses.put(variable,programAddress);
                        }
                        else
                        {
                            seKernel.Addresses.put(variable,programAddress);
                        }
                    }
                    break;
                case islandSoftwareEngine.KERNEL_CPU:
                    
                    // check general addresses for a value or pointer
                    if(this.Addresses!=null)
                    {
                        if(this.Addresses.containsKey(locationName))
                        {
                            programAddress = (String) this.Addresses.get(locationName);
                        }
                    }
                    
                    if(this.FunctionAddresses!=null)
                    {
                        if(this.FunctionAddresses.containsKey(locationName))
                        {
                            programAddress = (String) this.FunctionAddresses.get(locationName);
                        }
                    }
                    
                    if(!programAddress.equals(""))
                    {
                        if(this.LabelledValues!=null)
                        {
                            if(this.LabelledValues.containsKey(locationName))
                            {
                                this.Value = (String) this.LabelledValues.get(locationName);
                            }
                            else
                            {
                                this.Value = (String) this.LabelledValues.get(programAddress);
                            }
                        }
                    }

                    break;
            }
            
            return programAddress;
        }
        
        public void WriteValueToAddress(String valueVariable, String addyLabel, int type)
        {
            
        }
        
        public void WriteObjectFromAddress(String objectName,String  addressLabel)
        {
            
        }
        
        public void WriteAddressFromObject(String objectName, String addressLabel)
        {
            
        }
        
        public String ReadValueFromAddress(String addyLabel, int type)
        {
            return new String("");
        }
        
        public boolean SetUpCpuAddressSpace()
        {
            return false;
        }
        
        public boolean SetUpCpuStack()
        {
            return false;
        }
        
        public boolean CpuStarted()
        {
            boolean started = false;
            
            if(this.Cpu.GetEngineState() == islandProcessorEngine.PROC_ENGINE_STARTED)
            {
                started = true;
            }
            
            return started;
        }
        
        public String ProcessInstructions(String fromAddress, int procType, String what)
        {
            String r_address = "";
            
            try
            {
                // set up fragment end conditions
                if(this.Cpu.CyclingToValue()||this.Cpu.CyclingToStackEvent()||this.Cpu.CyclingToInterrupt())
                {
                    if(!CpuStarted())
                    {
                        // set up total bounds of program space
                        SetUpCpuAddressSpace();
                    
                        // layout all return addresses in stack
                        SetUpCpuStack();
                    }
                    
                    // set Kernel State
                    State = islandSoftwareEngine.KERNEL_RUNNING_CPU_INSTRUCTIONS;
                    
                    switch(this.Cpu.CycleToNextEventFromAddress(fromAddress))
                    {
                        case islandProcessorEngine.WAIT_FOR_KERNEL_ACTION:
                            break;
                        case islandProcessorEngine.WAIT_FOR_KERNEL_AWK:
                            break;
                        case islandProcessorEngine.WAIT_FOR_PROCESSOR_POINTS:
                            break;
                        case islandProcessorEngine.WAIT_FOR_EXTERNAL_FUNCTION:
                            break;
                        
                    }
                }
            }
            catch(Exception ex)
            {
                System.out.println("[ERROR] problems processing Cpu Instructions from:"+fromAddress);
            }
            
            return r_address;
        }
        
        public String ProcessInstructions(String fromAddress, int procType)
        {
            String r_address = "";
            
            try
            {
                switch(procType)
                {
                    case islandProcessorEngine.PROCESSOR_ARM_ARCH:
                        if(this.Cpu.GetMachineName().indexOf("ARM")>=0)
                        {
                            //this.Cpu.
                        }
                        break;
                }
            }
            catch(Exception ex)
            {
                System.out.println("[ERROR] problems processing Cpu Instructions from:"+fromAddress);
            }
            
            return r_address;
        }
        
        public boolean HandleProcessorEvent(int procEvent)
        {
            
            return false;
        }
        
        public int GetCPUStatus()
        {
            return this.Cpu.GetEngineStatus();
        }
        
        public Object GetCPUStackReturnValue()
        {
            return new Object();
        }
        
        public int GetCPUTypeFromName(String archName)
        {
            int cpuArchType = -1;
            
            if(this.MappedCpus !=null)
            {
                Integer cpuAT = (Integer) this.MappedCpus.get(archName);
                cpuArchType = cpuAT.intValue();
            }
            else
            {
                this.MappedCpus = new Hashtable();
            }
            
            return cpuArchType;
        }
        
        public String ResolveMemoryAddress(String Label)
        {
            String value = "";
            
            System.out.println("[INFO] looking address for memory label");
            
            value = (String) this.Addresses.get(Label);
            
            if(value == null)
            {
                
            }
            
            return value;
        }
        
        public String ResolveStaticAddress(String address)
        {
            String value = "";
            
            System.out.println("[INFO] looking up value for address:"+address.substring(address.indexOf("-0x")+3));
            
            value = (String) this.CStringAddresses.get(address.substring(address.indexOf("-0x")+3));
            
            if(value==null)
            {
                value = "";
            }
            
            return value;
        }
        
        public String ResolveFunctionAddress(String address)
        {
            String value = "";
            
            return value;
        }
        
        public boolean RunOperationsAtAddress(String address)
        {
            return false;
        }
    }

    public class GridScreenDetails
    {
        public String Id;
        public String GridName;
        public String GridLayoutOS;
        public String GridLayoutVersion;
        public String ScreenName;
        public Stack Screens;
        public String Event;
        public Stack Events;
        public DisplayLayoutDetails Layout;
        public Vector Layouts;
        public Hashtable Screen;
        public String Origin;
        public int GridIndex;
        public int GridColor;
        public int GridOriginX;
        public int GridOriginY;
        public int GridMaxX;
        public int GridMaxY;
        public boolean hasScreens;
        public boolean hasLayouts;
        public boolean debug;
        
        public GridScreenDetails()
        {
            Id = "";
            Origin = "";
            GridName = "";
            GridLayoutOS = "";
            GridLayoutVersion = "";
            Event = "";
            ScreenName = "";
            
            Events = null;
            Layout = null;
            Layouts = null;
            Screens = null;
            
            GridIndex = 0;
            GridColor = 0;
            GridOriginX = 0;
            GridOriginY = 0;
            GridMaxX = 0;
            GridMaxY = 0;
            
            hasLayouts = false;
            hasScreens = false;
            debug = true;
        }
        
        public void AddEventToStack()
        {
            if(this.Events == null)
            {
                this.Events = new Stack();
            }
            
            if((this.Event.indexOf("Init")>=0)&&(this.Event.indexOf(":")>=0))
            {
                String initLayout = "Init_" + this.Event.substring(Event.indexOf(":")+1);
                
                this.Events.addElement(initLayout);
            }
            
            if((this.Event.indexOf("Load")>=0)&&(this.Event.indexOf(":")>=0))
            {
                String loadScreen = "Load_" + this.Event.substring(Event.indexOf(":")+1);
                
                this.Events.addElement(loadScreen);
            }
        }
        
        public void AddEventToStack(String ev)
        {
            if(this.Events == null)
            {
                this.Events = new Stack();
            }
            
            if((ev.indexOf("Init")>=0)&&(ev.indexOf(":")>=0))
            {
                String initLayout = "Init_" + ev.substring(ev.indexOf(":")+1);
                
                this.Events.addElement(initLayout);
            }
            
            if((ev.indexOf("Load")>=0)&&(ev.indexOf(":")>=0))
            {
                String loadScreen = "Load_" + ev.substring(ev.indexOf(":")+1);
                
                this.Events.addElement(loadScreen);
            }
        }
        
        public void AddLayoutToScreen()
        {
            Vector screenLayouts = null;
            
            // if screen is blank
            if(this.Screen == null)
            {
                this.Screen = new Hashtable();
            }
            else
            {
                // get vector in hash
                screenLayouts = (Vector) this.Screen.get(this.ScreenName);
            }
            
            this.Layout.LayoutHeight = Layout.LayoutY * (displayHeight /this.GridMaxY); 
            this.Layout.LayoutWidth = Layout.LayoutX * (displayWidth / this.GridMaxX);
            
            
            if(screenLayouts != null)
            {
                //if(!screenLayouts.contains(Layout))
                if(!ScreenAlreadyHasLayout(Layout))
                {
                    screenLayouts.addElement(Layout);
                    this.Screen.put(this.ScreenName, screenLayouts);
                }
               
            }
            else
            {
                screenLayouts = new Vector();
                screenLayouts.addElement(Layout);
                this.Screen.put(this.ScreenName,screenLayouts);
            }
            
            if(this.debug)
            {
                for(Enumeration e = this.GetScreenLayouts(this.ScreenName); e.hasMoreElements();)
                {
                    DisplayLayoutDetails dispLD = (DisplayLayoutDetails)e.nextElement();
                    
                    if(dispLD !=null)
                    {
                        System.out.println("[INFO] Screen contains"+dispLD.Name);
                    
                        if(dispLD.Components !=null)
                        {
                            System.out.println("[INFO] Layout has "+ dispLD.Components.size() +"Components");
                        }
                        
                        if(dispLD.Graphics !=null)
                        {
                            System.out.println("[INFO] Layout has"+ dispLD.Graphics.size() +"Graphics");
                        }
                    }
                }
                                    
            }
        }
        
        public boolean ScreenAlreadyHasLayout(DisplayLayoutDetails Dld)
        {
            boolean layoutExists = false;
            
            for(Enumeration e = this.GetScreenLayouts(this.ScreenName); e.hasMoreElements();)
            {
                DisplayLayoutDetails dispLD = (DisplayLayoutDetails)e.nextElement();
            
                if(dispLD.Name.equals(Dld.Name))
                {
                    this.Screen.remove(Dld);
                    layoutExists = true;
                }
            }
            
            return layoutExists;
        }
        
        
        public void AddScreenToGrid()
        {
            if(this.Screens == null)
            {
                this.Screens = new Stack();
            }
            
            if(this.ScreenName!=null)
            {
                if(!this.Screens.contains(ScreenName))
                {
                    Screens.addElement(ScreenName);
                }
            }
        }
        
        public void AddScreenToGrid(String screen)
        {
            if(this.Screens == null)
            {
                this.Screens = new Stack();
            }
            
            if(screen!=null)
            {
                if(!this.Screens.contains(screen))
                {
                    Screens.addElement(screen);
                }
            }
        }
        
        /**
         * @return Uses stored grid info to lookup
         * and return the correct collection of layouts.
         */
        public Enumeration GetScreenLayouts(String lScreen)
        {
            if(Screens == null)
            {
                this.Screens = new Stack();
            }
            
            int indxForScreen  = Screens.indexOf((String)lScreen);
            Vector slayouts = null;
            
            // do we have layouts for this screen?
            if(indxForScreen>=0)
            {
                slayouts = (Vector) Screen.get(lScreen);
            
                if(slayouts==null)
                {
                    slayouts = new Vector();
                    Screen.put(lScreen,slayouts);
                }
            }
            else
            {
                slayouts = new Vector();
                Screens.addElement(lScreen);
                slayouts = (Vector) Screen.get(lScreen);
                
                if(slayouts==null)
                {
                    slayouts = new Vector();
                    Screen.put(lScreen,slayouts);
                }
            }
            
            return slayouts.elements();
        }
        
        public Stack GetGridScreens()
        {
            return this.Screens;
        }

        public int GetGridWidth()
        {
            int actualWidth = 0;
            int averageXWidth = displayWidth/this.GridMaxX;
            int actualXOrigin  = averageXWidth * this.GridOriginX;
            
            actualWidth = (averageXWidth * GridMaxX) - actualXOrigin;
            
            return actualWidth;
        }
        
        public int GetGridHeight()
        {
            int actualHeight = 0;
            int averageYHeight = displayHeight/this.GridMaxY;
            int actualYOrigin  = averageYHeight * this.GridOriginY;
            
            actualHeight = (averageYHeight * GridMaxY) - actualYOrigin;
            
            return actualHeight;
        
        }
        
        public int GetGridOriginX()
        {
            int actualWidth = 0;
            int averageXWidth = displayWidth/this.GridMaxX;
            int actualXOrigin  = averageXWidth * this.GridOriginX;
            
            return actualXOrigin;
        }
        
        public int GetGridOriginY()
        {
            int actualHeight = 0;
            int averageYHeight = displayHeight/this.GridMaxY;
            int actualYOrigin  = averageYHeight * this.GridOriginY;
            
            return actualYOrigin;
        }
        
        public boolean SetGridColor(String color)
        {
            boolean colorSet = false;
            
            if(color.equals("Black"))
            {
                this.GridColor = Color.BLACK;
                colorSet = true;
            }
            
            if(color.equals("White"))
            {
                this.GridColor = Color.WHITE;
                colorSet = true;
            }
            
            if(color.equals("Blue"))
            {
                this.GridColor = Color.BLUE;
                colorSet = true;
            }
            
            return colorSet;
        }
        
        public void SetScreenLayoutGgd(String ScreenName,String LayoutName,GraphicGuiDetails ggd)
        {
            try
            {
                // get screen Layouts
                for(Enumeration en = this.GetScreenLayouts(ScreenName); en.hasMoreElements();)
                {
                    DisplayLayoutDetails dld = (DisplayLayoutDetails) en.nextElement();
                    
                    if((dld.Components !=null)&&(dld.Graphics!=null))
                    {
                        if((!dld.Components.contains(ggd))&&(dld.Graphics.contains(ggd)))
                        {
                            if(dld.Name.equals(LayoutName))
                            {
                                dld.AddGraphicGuiDetails(ggd);
                            }
                        }
                    }
                    else
                    {
                        if(dld.Name.equals(LayoutName))
                        {
                            dld.AddGraphicGuiDetails(ggd);
                        }
                    }
                }
            }
            catch(Exception ex)
            {
                System.out.println("[ERROR]problems setting ScreenLayoutGGD:"+ex.toString());
            }
        }
    }
    
    
    public class DisplayLayoutDetails
    {
        public String Id;
        public String Name;
        public String Event;
        public String ScreenName;
        public GraphicGuiDetails Ggd;
        public Stack Events;
        public Vector Graphics;
        public Vector Components;
        public double LayoutX;
        public double LayoutY;
        public double LayoutWidth;
        public double LayoutHeight;
        public int GridMaxX;
        public int GridMaxY;
        public double LayoutPlacementX;
        public double LayoutPlacementY;
        public int indx;
        public int LayoutColor;
        public boolean hasGraphics;
        public boolean hasComponents;
        
        public DisplayLayoutDetails()
        {
            Id = "";
            Name = "";
            Event = "";
            ScreenName = "";
            
            Ggd = null;
            Events = null;
            
            LayoutX = 0;
            LayoutY = 0;
            LayoutWidth = 0;
            LayoutHeight = 0;
            GridMaxX = 0;
            GridMaxY = 0;
            LayoutPlacementX = 0;
            LayoutPlacementY = 0;
            LayoutColor = Color.BLACK;
            indx = 0;
            
            hasGraphics = false;
            hasComponents = false;
        }

        public void AddEventToStack()
        {
            if(this.Events == null)
            {
                this.Events = new Stack();
            }
            
            if((this.Event.indexOf("Init")>=0)&&(this.Event.indexOf(":")>=0))
            {
                String initLayout = "Init_" + this.Event.substring(Event.indexOf(":")+1);
                
                this.Events.addElement(initLayout);
            }
        }
        
        public void AddEventToStack(String ev)
        {
            if(this.Events == null)
            {
                this.Events = new Stack();
            }
            
            if((ev.indexOf("Init")>=0)&&(ev.indexOf(":")>=0))
            {
                String initLayout = "Init_" + ev.substring(ev.indexOf(":")+1);
                
                this.Events.addElement(initLayout);
            }
        }

        public void AddGraphicGuiDetails(GraphicGuiDetails gd)
        {
            double averageWidth = displayWidth/this.GridMaxX;
            double averageHeight = displayHeight/this.GridMaxY;
            
            this.LayoutHeight = averageHeight * this.LayoutY;
            this.LayoutWidth = averageWidth * this.LayoutX;
             
            double layoutXPlacement= averageWidth * this.LayoutPlacementX;
            double layoutYPlacement= averageHeight  * this.LayoutPlacementY;
            
            if(gd!=null)
            {
                
                if((gd.PlacementX() < this.GridMaxX)&&(gd.PlacementY() < this.GridMaxY))
                {
                    String xVal = Double.toString(averageWidth * gd.PlacementX());
                    String yVal = Double.toString(averageHeight * gd.PlacementY());
                        
                    if(
                        (Double.parseDouble(xVal)>=0.0)&&(Double.parseDouble(xVal)<=LayoutWidth)&&
                        (Double.parseDouble(yVal)>=0.0)&&(Double.parseDouble(yVal)<=LayoutHeight)&&
                        (!gd.PlacementSet)
                      )
                    {
                        /*
                        if(Double.parseDouble(xVal) == 0.0)
                        {
                            xVal = Double.toString(layoutXPlacement);
                        }

                        if(Double.parseDouble(yVal) == 0.0)
                        {
                            yVal = Double.toString(layoutYPlacement);
                        }
                        */
                        // New Placement
                        gd.Placement = xVal + ":" + yVal;
                        gd.PlacementSet = true;
                    }
                }
            
                this.Ggd = gd;
                
                // if no height and with replace with default
                if((Ggd.height == 0)&&(Ggd.width == 0))
                {
                    Ggd.height = averageHeight;
                    Ggd.width = averageWidth;  
                }
                
                if(Ggd.Height.equals("LayoutHeight"))
                {
                    Ggd.height = LayoutHeight;
                }
                 
                if(Ggd.Height.equals("LayoutWidth"))
                {
                    Ggd.width = LayoutWidth;
                }
                   
                if(gd.ComponentsObject!=null)
                {
                     this.hasComponents = true;
                }
                
                if(gd.GraphicsObject!=null)
                {
                     this.hasGraphics = true;
                }
                
                if(gd.ComponentsObject!=null)
                {
                    if(this.Components==null)
                    {
                        this.Components = new Vector();
                    }
                    
                    if(!this.Components.contains(this.Ggd))
                    {
                        this.Components.addElement(this.Ggd);
                    }
                }
                else
                {
                    if(this.Graphics == null)
                    {
                        this.Graphics = new Vector();
                    }
                    
                    //if(!this.Graphics.contains(this.Ggd))
                    if(!this.GraphicExists(this.Ggd))
                    {
                        this.Graphics.addElement(this.Ggd);
                        
                        System.out.println("[INFO] added Graphic:"+Ggd.Name+" at "+Ggd.PlacementX()+"&"+Ggd.PlacementY());
                    }
                    else
                    {
                        System.out.println("[INFO] didn't add Graphic:"+Ggd.Name+" at "+Ggd.PlacementX() + "&" + Ggd.PlacementY());
                    }
                }
            }
        }
        
        public boolean GraphicExists(GraphicGuiDetails g)
        {
            boolean exists = false;
            
            if(g!=null)
            {
                for(Enumeration e =this.Graphics.elements();e.hasMoreElements();)
                {
                    GraphicGuiDetails gg = (GraphicGuiDetails) e.nextElement();
                    
                    if(gg!=null)
                    {
                        if((g.Name!=null)&&(gg.Name!=null))
                        {
                            if(g.Name.equals(gg.Name))
                            {
                                if((g.Placement!=null)&&(gg.Placement!=null))
                                {
                                    if(g.Placement.equals(gg.Placement))
                                    {
                                        exists = true;
                                        /*
                                        if((g.GraphicsObject!=null)&&(gg.GraphicsObject!=null))
                                        {
                                            if(g.GraphicsObject.equals(gg.GraphicsObject))
                                            {
                                                if((g.Events!=null)&&(gg.Events!=null))
                                                {
                                                    if(g.Events.equals(gg.Events))
                                                    {
                                                        if((g.Event!=null)&&(gg.Event!=null))
                                                        {
                                                            if(g.Event.equals(gg.Event))
                                                            {
                                                                exists = true
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        */
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            return exists;
        }

        public void SetGridLayoutGraphicEvent(String event, String name)
        {
            int eventIndx = 0;
            String graphicName = "";
            
            if(event.indexOf("[")>=0)
            {
                eventIndx = Integer.parseInt(event.substring(event.indexOf("[")+1,event.indexOf("]")));
            }
            
            if(name.indexOf("_")>=0)
            {
                graphicName = name.substring(0,name.indexOf("_"));
            }
            else
            {
                graphicName = name;
            }
            
            GraphicGuiDetails g = (GraphicGuiDetails) this.Graphics.elementAt(eventIndx);
            
            if(g!=null)
            {
                System.out.println("[INFO] graphicName is:"+graphicName+"&name is:"+g.Name);
                
                if(graphicName.equals(g.Name))
                {
                    g.Event = event;
                    g.AddEventToStack();
                }
            }
        }
        
        public void SetGridLayoutGraphicColor(int ggdColor, String name)
        {
            int colorIndx = 0;
            String graphicName = "";
            String ObColor = "BLACK";
            
            
            if(Color.BLACK != ggdColor)
            {
                if(ggdColor == Color.WHITE)
                {
                    ObColor = "WHITE";
                }
            }
            
            if(ObColor.indexOf("[")>=0)
            {
                colorIndx = Integer.parseInt(ObColor.substring(ObColor.indexOf("[")+1,ObColor.indexOf("]")));
            }
            
            if(name.indexOf("_")>=0)
            {
                graphicName = name.substring(0,name.indexOf("_"));
            }
            else
            {
                graphicName = name;
            }
            
            GraphicGuiDetails g = (GraphicGuiDetails) this.Graphics.elementAt(colorIndx);
            
            if(g!=null)
            {
                System.out.println("[INFO] graphicName is:"+graphicName+"&name is:"+g.Name);
                
                if(graphicName.equals(g.Name))
                {
                    g.ObjectColor = ggdColor;
                }
            }
        }
        
        public void SetGridLayoutGraphicHeight(String ggdHeight, String name)
        {
            int heightIndx = 0;
            String graphicName = "";
            double averageHeight = displayHeight/this.GridMaxY;
           
            
            if(ggdHeight.indexOf("[")>=0)
            {
                heightIndx = Integer.parseInt(ggdHeight.substring(ggdHeight.indexOf("[")+1,ggdHeight.indexOf("]")));
            }
            
            if(name.indexOf("_")>=0)
            {
                graphicName = name.substring(0,name.indexOf("_"));
            }
            else
            {
                graphicName = name;
            }
            
            GraphicGuiDetails g = (GraphicGuiDetails) this.Graphics.elementAt(heightIndx);
            
            if(g!=null)
            {
                System.out.println("[INFO] graphicName is:"+graphicName+"&name is:"+g.Name);
                
                if(graphicName.equals(g.Name))
                {
                    g.Height = ggdHeight.substring(ggdHeight.indexOf("]-")+2);
                    g.height = Double.parseDouble(g.Height) * averageHeight;
                }
            }
        }

        public void SetGridLayoutComponentHeight(String ggdHeight, String name)
        {
            int heightIndx = 0;
            String componentName = "";
            double averageHeight = displayHeight/this.GridMaxY;
           
            
            if(ggdHeight.indexOf("[")>=0)
            {
                heightIndx = Integer.parseInt(ggdHeight.substring(ggdHeight.indexOf("[")+1,ggdHeight.indexOf("]")));
            }
            
            if(name.indexOf("_")>=0)
            {
                componentName = name.substring(0,name.indexOf("_"));
            }
            else
            {
                componentName = name;
            }
            
            GraphicGuiDetails g = (GraphicGuiDetails) this.Components.elementAt(heightIndx);
            
            if(g!=null)
            {
                System.out.println("[INFO] componentName is:"+componentName+"&name is:"+g.Name);
                
                if(componentName.equals(g.Name))
                {
                    g.Height = ggdHeight.substring(ggdHeight.indexOf("]-")+2);
                    g.height = Double.parseDouble(g.Height) * averageHeight;
                }
            }
        }

        public void SetGridLayoutGraphicWidth(String ggdWidth, String name)
        {
            int widthIndx = 0;
            String graphicName = "";
            double averageWidth = displayWidth/this.GridMaxX;
            
            
            if(ggdWidth.indexOf("[")>=0)
            {
                widthIndx = Integer.parseInt(ggdWidth.substring(ggdWidth.indexOf("[")+1,ggdWidth.indexOf("]")));
            }
            
            if(name.indexOf("_")>=0)
            {
                graphicName = name.substring(0,name.indexOf("_"));
            }
            else
            {
                graphicName = name;
            }
            
            GraphicGuiDetails g = (GraphicGuiDetails) this.Graphics.elementAt(widthIndx);
            
            if(g!=null)
            {
                System.out.println("[INFO] graphicName is:"+graphicName+"&name is:"+g.Name);
                
                if(graphicName.equals(g.Name))
                {
                    g.Width = ggdWidth.substring(ggdWidth.indexOf("]-")+2);
                    g.width = Double.parseDouble(g.Width) * averageWidth;
                }
            }
        }
        
        public void SetGridLayoutComponentWidth(String ggdWidth, String name)
        {
            int widthIndx = 0;
            String componentName = "";
            double averageWidth = displayWidth/this.GridMaxX;
            
            
            if(ggdWidth.indexOf("[")>=0)
            {
                widthIndx = Integer.parseInt(ggdWidth.substring(ggdWidth.indexOf("[")+1,ggdWidth.indexOf("]")));
            }
            
            if(name.indexOf("_")>=0)
            {
                componentName = name.substring(0,name.indexOf("_"));
            }
            else
            {
                componentName = name;
            }
            
            GraphicGuiDetails g = (GraphicGuiDetails) this.Components.elementAt(widthIndx);
            
            if(g!=null)
            {
                System.out.println("[INFO] componentName is:"+componentName+"&name is:"+g.Name);
                
                if(componentName.equals(g.Name))
                {
                    g.Width = ggdWidth.substring(ggdWidth.indexOf("]-")+2);
                    g.width = Double.parseDouble(g.Width) * averageWidth;
                }
            }
        }
        
        public Vector GetLayoutGraphics()
        {
            if(this.Graphics == null)
            {
                this.Graphics = new Vector();
            }
            
            return this.Graphics;
        }
        
        public Vector GetLayoutComponents()
        {
            if(this.Components == null)
            {
                this.Components = new Vector();
            }                
            
            return this.Components;
        }
        
        public boolean SetLayoutColor(String color)
        {
            boolean colorSet = false;
            
            if(color.equals("Black"))
            {
                this.LayoutColor = Color.BLACK;
            }
            
            if(color.equals("White"))
            {
                this.LayoutColor = Color.WHITE;
            }
            
            return colorSet;
        }   
        
        public double GetLayoutWidth()
        {
            
            return this.LayoutWidth;
        }
       
        public double GetLayoutHeight()
        {
            return this.LayoutHeight;
        }
        
        public double GetPlacementX()
        {
            double averageWidth = displayWidth/this.GridMaxX;
            
            return this.LayoutPlacementX * averageWidth;
        }
       
        public double GetPlacementY()
        {
            double averageHeight = displayHeight/this.GridMaxY;
            
            return this.LayoutPlacementY * averageHeight;
        }
    }
    
    public class GraphicGuiDetails  
    {
        public String Id;
        public String Name;
        public String Event;
        public String Location;
        public String Placement;
        public String ObjectText;
        
        public Object GraphicsObject;
        public Object ComponentsObject;
        public Object Manager;
        
        public Stack Events;
        
        public int indx;
        public int xPlacementIncrement;
        public int yPlacementIncrement;
        public int ObjectColor;
        public int LayoutIndx;
        
        public double width;
        public double height;
        
        public String Width;
        public String Height;
        
        public boolean BelongsToLayout;
        public boolean PlacementSet;
        public boolean HasText;
        
        public GraphicGuiDetails()
        {
            this.Events = null;
            this.xPlacementIncrement = 0;
            this.yPlacementIncrement = 0;
            
            this.ComponentsObject = null;
            this.GraphicsObject = null;
            this.ObjectColor = Color.BLACK;
            this.Location = "";
            this.Height = "";
            this.Width = "";
            this.Event = "";
            this.Name = "";
            this.Id = "";
            this.indx = 0;
            this.height = 0;
            this.width = 0;
            this.LayoutIndx = 0;
        
            this.BelongsToLayout = false;
            this.PlacementSet = false;
        }
        
        public void AddEventToStack(String ev)
        {
            if(this.Events == null)
            {
                this.Events = new Stack();
            }
            
            if((ev.indexOf("Draw")>=0)&&(ev.indexOf("Precise")>=0))
            {
                // add Event to collection
                if(!Events.contains("Draw_Precise"))
                {
                    Events.addElement("Draw_Precise");
                }
                
                if(Events.contains("Draw_Relative"))
                {
                    Events.removeElement("Draw_Relative");
                }
            }
            
            if((ev.indexOf("Draw")>=0)&&(ev.indexOf("Relative")>=0))
            {
                if(!Events.contains("Draw_Relative"))
                {
                    Events.addElement("Draw_Relative");
                }
                
                if(Events.contains("Draw_Precise"))
                {
                    Events.removeElement("Draw_Precise");
                }
            }
            
            if(ev.indexOf("Draw")<0)
            {
                Events.addElement(ev);
            }
        }

        public void AddEventToStack()
        {
            if(this.Events == null)
            {
                this.Events = new Stack();
            }
            
            if((Event.indexOf("Draw")>=0)&&(Event.indexOf("Precise")>=0))
            {
                // add Event to collection
                if(this.Events == null)
                {
                    this.Events = new Stack();
                }
                    
                if(!Events.contains("Draw_Precise"))
                {
                    this.Events.addElement("Draw_Precise");
                }
                
                if(Events.contains("Draw_Relative"))
                {
                    Events.removeElement("Draw_Relative");
                }
            }
            
            if((Event.indexOf("Draw")>=0)&&(Event.indexOf("Relative")>=0))
            {
                // add Event to collection
                if(this.Events == null)
                {
                    this.Events = new Stack();
                }
                    
                if(!Events.contains("Draw_Relative"))
                {
                    this.Events.addElement("Draw_Relative");
                }
                
                if(Events.contains("Draw_Precise"))
                {
                    Events.removeElement("Draw_Precise");
                }
            }
            /*
            if(Event.indexOf("Draw")<0)
            {
                Events.addElement(this.Event);
            }
            */
            
            if((Event.indexOf("Init")>=0)&&(Event.indexOf("Set")>=0)&&(Event.indexOf("0x0")>=0))
            {
                if(this.Events == null)
                {
                    this.Events = new Stack();
                }
                
                Events.addElement(this.Event);
            }
            
            
            if((Event.indexOf("Click")>=0)&&(Event.indexOf("Run")>=0))
            {
                if(this.Events == null)
                {
                    this.Events = new Stack();
                }
                
                Events.addElement(this.Event);
            }
        }
        
        public double LocationX()
        {
            String xVal;
            double x = 0;
            
            if(this.Location.indexOf(":")>=0)
            {
                xVal = this.Location.substring(0,this.Location.indexOf(":"));
                x = Integer.valueOf(xVal).doubleValue();
            }
            
            return x; 
        }
        
        public double LocationY()
        {
            String yVal;
            double y = 0;
            
            if(this.Location.indexOf(":")>=0)
            {
                yVal = this.Location.substring(this.Location.indexOf(":")+1);
                y = Integer.valueOf(yVal).doubleValue();
            }
            
            return y; 
        }
        
        public void SetPlacementIncrement()
        {
            // y+
            if(this.Placement.indexOf("+")>this.Placement.indexOf(":"))
            {
                this.yPlacementIncrement = Integer.valueOf(Placement.substring(this.Placement.indexOf(":")+1,this.Placement.indexOf("+"))).intValue();
                //this.xPlacementIncrement = Integer.valueOf(Placement.substring(this.Placement.indexOf("_")+1,this.Placement.indexOf(":"))).intValue();
           }
            else
            // x+
            {
                //this.xPlacementIncrement = Integer.valueOf(Placement.substring(this.Placement.indexOf("_")+1,this.Placement.indexOf("+"))).intValue();
                this.yPlacementIncrement = Integer.valueOf(Placement.substring(this.Placement.indexOf(":")+1)).intValue();
            }
        }
        
        public double PlacementX()
        {
            String xVal;
            double x = 0;
            
            try
            {
                if(this.Placement !=null)
                {
                    if(this.Placement.indexOf(":")>=0)
                    {
                        if(this.Placement.indexOf("+")<0)
                        {
                            xVal = this.Placement.substring(0,this.Placement.indexOf(":"));
                            //x = Integer.valueOf(xVal).intValue();
                            x = Double.parseDouble(xVal);
                        }
                        else
                        {
                            if(this.Placement.indexOf("+")<this.Placement.indexOf(":"))
                            {
                                xVal = this.Placement.substring(0,this.Placement.indexOf("+"));
                                //x = Integer.valueOf(xVal).intValue();
                                x = Double.parseDouble(xVal);
                        
                            }
                            else
                            {
                                xVal = this.Placement.substring(0,this.Placement.indexOf(":"));
                                //x= Integer.valueOf(xVal).intValue();
                                x = Double.parseDouble(xVal);
                            }
                        }
                    }
                }
            }
            catch(Exception ex)
            {
                System.out.println("[ERROR] problems getting placement x:"+ex.toString());
            }
            
            return x; 
        }
        
        public double PlacementY()
        {
            String yVal;
            double y = 0;

            try
            {
                if(this.Placement !=null)
                {
                    if(this.Placement.indexOf(":")>=0)
                    {
                        if(this.Placement.indexOf("+")<0)
                        {
                            yVal = this.Placement.substring(this.Placement.indexOf(":")+1);
                            //y = Integer.valueOf(yVal).intValue();
                            y = Double.parseDouble(yVal);
                        }
                        else
                        {
                            if(this.Placement.indexOf("+")>this.Placement.indexOf(":"))
                            {
                                yVal = this.Placement.substring(this.Placement.indexOf(":")+1,this.Placement.indexOf("+"));
                                //y = Integer.valueOf(yVal).intValue();
                                y = Double.parseDouble(yVal);
                            }
                            else
                            {
                                yVal = this.Placement.substring(this.Placement.indexOf(":")+1);
                                //y = Integer.valueOf(yVal).intValue();
                                y = Double.parseDouble(yVal);
                            }
                        }
                    }
                }
            }
            catch(Exception ex)
            {
                System.out.println("[ERROR] problems getting placement Y:"+ex.toString());
            }
            return y; 
        }
        
        public void SetColor(String colorName)
        {
            if(colorName.equals("BLACK"))
            {
                this.ObjectColor = Color.BLACK;
            }
            
            if(colorName.equals("WHITE"))
            {
                this.ObjectColor = Color.WHITE;
            }
        }
        

    };
    
    // public functions
    public static String urlEncode(String sUrl)   
    {  
        StringBuffer urlOK = new StringBuffer();  
        for(int i=0; i<sUrl.length(); i++)   
        {  
            char ch=sUrl.charAt(i);  
            switch(ch)  
            {  
                case '<': urlOK.append("%3C"); break;  
                case '>': urlOK.append("%3E"); break;  
                case '/': urlOK.append("%2F"); break;  
                case ' ': urlOK.append("%20"); break;  
                case ':': urlOK.append("%3A"); break;  
                case '-': urlOK.append("%2D"); break;  
                case '=': urlOK.append("%3D"); break;
                default: urlOK.append(ch); break;  
            }   
        }  
        
        return urlOK.toString();  
    }  
    
    // Software Type
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
    
    // Engine Status
    public static final int WAIT_FOR_DETAILS = 9;
    public static final int WAIT_FOR_DOWNLOAD = 10;
    public static final int WAIT_FOR_LOCAL = 11;
    public static final int WAIT_FOR_USER_ACTION = 12;
    public static final int WAIT_FOR_ENGINE_STOP = 13;
    public static final int WAIT_FOR_ENGINE_TRANSLATION = 14;
    public static final int WAIT_FOR_ENGINE_OPERATION = 15;
    public static final int WAIT_FOR_USER_AWK = 16;
    public static final int WAIT_FOR_TRANSLATION_POINTS = 17;
    public static final int WAIT_FOR_ADDRESS_POINTS = 18;
    public static final int WAIT_FOR_RESOURCES_DOWNLOAD = 19;
    public static final int WAIT_FOR_OPERATIONS_DOWNLOAD = 20;
    public static final int APP_ENGINE_STOPPED = 21;
    public static final int APP_ENGINE_STARTED = 22;
    public static final int WAIT_FOR_ENGINE_START = 75;
    
    // Translation Types
    public static final int USER_DEVICE_EVENT = 23; // Event coming from the device not necessarily app related
    public static final int USER_DEVICE_SELECTION_EVENT = 24;
    public static final int USER_DEVICE_KEY_EVENT = 25;
    public static final int USER_DEVICE_MENU_EVENT = 75;
    
    public static final int APP_ICON_GRAPHIC = 26;
    public static final int APP_BACKGROUND_GRAPHIC = 27;
    public static final int APP_BMP_GRAPHICS = 28;
    public static final int APP_BMP_GRAPHIC = 29;
    public static final int APP_PNG_GRAPHICS = 30;
    public static final int APP_PNG_GRAPHIC = 31;
    public static final int APP_AIFF_AUDIO = 32;
    public static final int APP_TEXT_GRAPHIC = 33;
    public static final int APP_TEXT_GRAPHICS = 34;
    public static final int APP_GRAPHIC_HEIGHT = 35;
    public static final int APP_GRAPHIC_WIDTH = 36;
    public static final int APP_GRAPHIC_PLACEMENT = 37;
    public static final int APP_GRAPHIC_PLACEMENTS = 38;
    public static final int APP_GRAPHIC_LOCATION = 39;
    public static final int APP_GRAPHIC_COLOR = 40;
    public static final int APP_GRAPHIC_EVENT = 41;
    public static final int APP_GUI_PLACEMENTS = 42; 
    public static final int APP_GUI_PLACEMENT = 43; 
    public static final int APP_GUI_EVENT = 44; // Event coming from a Gui Element
    public static final int APP_GUI_COLOR = 45; 
    public static final int APP_GUI_WIDTH = 46;
    public static final int APP_GUI_HEIGHT = 47;
    public static final int APP_GRID_ORIGIN = 48;
    public static final int APP_GRID_MAX = 49;
    public static final int APP_GRID_LAYOUT = 50;
    public static final int APP_GRID_LAYOUT_ORIGIN = 51;
    public static final int APP_GRID_LAYOUT_COLOR = 52;
    public static final int APP_GRID_LAYOUT_EVENT = 76;
    public static final int APP_GRID_COLOR = 53;
    public static final int APP_GRID_EVENT = 54;
    public static final int APP_GRID = 55;
    public static final int APP_CONTENT_MANAGEMENT = 56;
    public static final int APP_CONTENT_EVENT = 57; // Event coming from a file (most likely)
    public static final int APP_USER_EVENT = 58; // Event coming from user action during app use
    public static final int APP_USER_AWK = 59;
    public static final int APP_API_FUNCTION = 76;
    public static final int APP_API_FRAMEWORK = 77;
    public static final int APP_VENDOR_LIB = 78;
    public static final int APP_STATIC_ADDRESS = 79;
   
    // Application Status
    public static final int APP_DETAILS_READY = 60;
    public static final int APP_DOWNLOAD_COMPLETED = 61;
    public static final int APP_OPERATION_COMPLETED = 62;
    public static final int APP_TRANSLATION_COMPLETED = 63;
    public static final int APP_ADDRESS_LOOKUP_COMPLETED = 64;
    public static final int APP_OPERATIONS_DOWNLOADED = 65;
    public static final int APP_RESOURCES_DOWNLOADED = 66;
    public static final int APP_TRANSLATION_POINTS_READY = 67;
    public static final int APP_ADDRESS_POINTS_READY = 68;
    
    // Kernel Responsibilities
    public static final int KERNEL_IO = 69;
    public static final int KERNEL_IRQ = 70;
    public static final int KERNEL_NETWORK = 71;
    public static final int KERNEL_MEMORY = 72;
    public static final int KERNEL_MULTITASKTING = 73;
    public static final int KERNEL_CPU = 74;
    
    // Kernel States
    public static final int KERNEL_RUNNING_CPU_INSTRUCTIONS =0;
    public static final int KERNEL_HANDLING_CPU_EVENT = 1;
    public static final int KERNEL_RUNNING_API_FUNCTION = 2;
    public static final int KERNEL_HANDLING_API_LOOKUP_EVENT = 3;
    public static final int KERNEL_RESOLVING_MEMORY_ADDRESS = 4;
    public static final int KERNEL_HANDLING_ADDRESS_LOOKUP_EVENT = 5;
    public static final int KERNEL_UPDATING_STACK = 6;
    public static final int KERNEL_HANDLING_STACK_EVENT = 7;
    public static final int KERNEL_WAITING_ON_EVENT = 8;
    public static final int KERNEL_HANDLING_NETWORK_LOOKUP_EVENT = 9;
    public static final int KERNEL_RESOLVING_NETWORK_ADDRESS = 10;
    public static final int KERNEL_HANDLING_IO_EVENT = 11;
    public static final int KERNEL_FIRING_IO_EVENT = 12;
    public static final int KERNEL_WRITING_TO_OBJECT = 13;
    public static final int KERNEL_READING_FROM_OBJECT = 14;
    
    public islandSoftwareEngine(int type) 
    {   
        this.engineType = type;
        this.debug = true;
        this.displayHeight = 400;
        this.displayWidth = 200;
    }

    public islandSoftwareEngine(int eType, int sType) 
    {    
        this.engineType = eType;
        this.softwareType = sType;
        this.debug = true;
        this.displayHeight = 400;
        this.displayWidth = 200;
    }

    public islandSoftwareEngine(int eType, int sType, boolean needsStack) 
    {    
        this._usesVirtualStack = needsStack;
        this.engineType = eType;
        this.softwareType = sType;
        this.debug = true;
        this.displayHeight = 400;
        this.displayWidth = 200;
    }
    
    public islandSoftwareEngine(int eType, int sType, int height, int width) 
    {    
        this.engineType = eType;
        this.softwareType = sType;
        this.debug = true;
        this.displayHeight = height;
        this.displayWidth = width;
    }
    
    public islandSoftwareEngine(int eType, int sType, int height, int width, boolean needsStack) 
    {    
        this._usesVirtualStack = needsStack;
        this.engineType = eType;
        this.softwareType = sType;
        this.debug = true;
        this.displayHeight = height;
        this.displayWidth = width;
    }
    
    /**
     * Function loops through events in component
     * list and graphic list and initializes the objects
     * it finds.
     */
    public void startEngine()
    {
        String ev = "";
        String initValue = "";

        //this.currentStatus = WAIT_FOR_ENGINE_START;
        
        try
        {
            
            if(this.debug)
            {
                System.out.println("[INFO] about to fire grid init events");
            }
            
            // Load Init labelled Layouts
            if(this._hasLayouts)
            {
                try
                {
                    // loop through all components
                    for(Enumeration e = seGrids.elements(); e.hasMoreElements();)
                    {
                        
                        GridScreenDetails gsd = (GridScreenDetails) e.nextElement();
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] the name for the grid we're on is:"+gsd.GridName);
                            System.out.println("[INFO] the name for the inital screen that will load is:"+gsd.ScreenName);
                        }
                        
                        // Set the SreenName to the init:
                        for(Enumeration s = gsd.Events.elements(); s.hasMoreElements();)
                        {
                            String initEvent = (String) s.nextElement();
                            
                            if(this.debug)
                            {
                                System.out.println("[INFO] this is a grid event:"+initEvent);
                            }
                            
                            if(initEvent.indexOf("Init")>=0)
                            {
                                gsd.ScreenName = initEvent.substring(initEvent.indexOf("_")+1);
                            }
                        }
                        
                        for(Enumeration en  = gsd.GetGridScreens().elements(); en.hasMoreElements();)
                        {
                            String screenName = (String) en.nextElement();
                            
                            if(this.debug)
                            {
                                System.out.println("[INFO] the name for the screen we're on is:"+screenName);
                            }
                            
                            for(Enumeration enu = gsd.GetScreenLayouts(screenName); enu.hasMoreElements();)
                            {
                                DisplayLayoutDetails dld = (DisplayLayoutDetails) enu.nextElement();
                            
                                if(this.debug)
                                {
                                    System.out.println("[INFO] the name for the layout we're on is:"+dld.Name);
                                }
                                
                                if(dld !=null)
                                {
                                
                                    if(dld.Graphics !=null)
                                    {
                                        for(Enumeration enm = dld.GetLayoutGraphics().elements(); enm.hasMoreElements();)
                                        {
                                            GraphicGuiDetails ggd = (GraphicGuiDetails) enm.nextElement();
                                            
                                            if(ggd != null)
                                            {
                                                if(this.debug)
                                                {
                                                    System.out.println("[INFO] the name for the graphic we're on is:"+ggd.Name);
                                                }
                                                
                                                if(ggd.Events != null)
                                                {
                                                    for(Enumeration em = ggd.Events.elements(); em.hasMoreElements();)
                                                    {
                                                        //if(ggd.GraphicsObject instanceof Bitmap)
                                                        {
                                                            ev = (String) em.nextElement();
                                                            
                                                            if(this.debug)
                                                            {
                                                                System.out.println(ggd.Name + "-Event:"+ev);
                                                            }

                                                            if(ev.indexOf("Init")>=0)
                                                            {
                                                                //ggd.
                                                                initValue = ev.substring(ev.indexOf(":")+1);
                                                
                                                                // resolve static addresses
                                                                if(this._hasStaticAddresses)
                                                                {
                                                                    if((initValue.indexOf("Set-0x0")>=0)||(initValue.indexOf("]-0x0")>=0))
                                                                    {
                                                                        initValue = seKernel.ResolveStaticAddress(initValue).replace('\n',' ').trim();
                                                                    }
                                                                }
                                                
                                                                ggd.HasText = true;
                                                                ggd.ObjectText = initValue;
                                                            }
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    if(ggd.Event !=null)
                                                    {
                                                        ev = (String) ggd.Event;
                                                        
                                                        if(this.debug)
                                                        {
                                                            System.out.println(ggd.Name + "-Event:"+ev);
                                                        }
                                                        
                                                        if(ev.indexOf("Init")>=0)
                                                        {
                                                            //ggd.
                                                            initValue = ev.substring(ev.indexOf(":")+1);
                                                
                                                                // resolve static addresses
                                                                if(this._hasStaticAddresses)
                                                                {
                                                                    if((initValue.indexOf("Set-0x0")>=0)||(initValue.indexOf("]-0x0")>=0))
                                                                    {
                                                                        initValue = seKernel.ResolveStaticAddress(initValue).replace('\n',' ').trim();
                                                                    }
                                                                }
                                                
                                                                ggd.HasText = true;
                                                                ggd.ObjectText = initValue;
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
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems firing Layout startup events:"+ex.toString());
                    }
                }
            }
            
            if(this.debug)
            {
                System.out.println("[INFO] about to fire component init events");
            }
            
            if(this._hasComponents)
            {
                // loop through all components
                for(Enumeration en = seComponents.elements(); en.hasMoreElements();)
                {
                    GraphicGuiDetails ggd = (GraphicGuiDetails) en.nextElement();
                    
                    if(ggd != null)
                    {
                        if(ggd.Events != null)
                        {
                            for(Enumeration em = ggd.Events.elements(); em.hasMoreElements();)
                            {
                                ev = (String) em.nextElement();
                                
                                if(ev.indexOf("Init")>=0)
                                {
                                    if(ggd.ComponentsObject instanceof BasicEditField)
                                    {
                                        BasicEditField bef = (BasicEditField) ggd.ComponentsObject;
                                        initValue = ev.substring(ev.indexOf(":")+1);
                                        
                                        // resolve static addresses
                                        if(this._hasStaticAddresses)
                                        {
                                            if(initValue.indexOf("Set-0x0")>=0)
                                            {
                                                initValue = seKernel.ResolveStaticAddress(initValue).replace('\n',' ').trim();
                                            }
                                        }
                                        
                                        bef.setText(initValue);
                                        ggd.ComponentsObject = bef;
                                    }
                                    
                                    if(ggd.ComponentsObject instanceof RichTextField)
                                    {
                                        RichTextField rtf = (RichTextField) ggd.ComponentsObject;
                                        initValue = ev.substring(ev.indexOf(":")+1);
                                        
                                        // resolve static addresses
                                        if(this._hasStaticAddresses)
                                        {
                                            if(initValue.indexOf("Set-0x0")>=0)
                                            {
                                                initValue = seKernel.ResolveStaticAddress(initValue).replace('\n',' ').trim();
                                            }
                                        }
                                        
                                        rtf.setText(initValue);
                                        ggd.ComponentsObject = rtf;
                                    }
                                }
                            }
                        }
                        else
                        {
                            if(ggd.Event !=null)
                            {
                                ev = (String) ggd.Event;
                                
                                if(ev.indexOf("Init")>=0)
                                {
                                    if(ggd.ComponentsObject instanceof BasicEditField)
                                    {
                                        BasicEditField bef = (BasicEditField) ggd.ComponentsObject;
                                        initValue = ev.substring(ev.indexOf(":")+1);
                                        
                                        // resolve static addresses
                                        if(this._hasStaticAddresses)
                                        {
                                            if(initValue.indexOf("Set-0x0")>=0)
                                            {
                                                initValue = seKernel.ResolveStaticAddress(initValue).replace('\n',' ').trim();
                                            }
                                        }
                                        
                                        //rtf.setText(initValue);
                                        bef.setText(initValue);
                                        ggd.ComponentsObject = bef;
                                    }
                                    
                                    if(ggd.ComponentsObject instanceof RichTextField)
                                    {
                                        RichTextField rtf = (RichTextField) ggd.ComponentsObject;
                                        initValue = ev.substring(ev.indexOf(":")+1);
                                        
                                        // resolve static addresses
                                        if(this._hasStaticAddresses)
                                        {
                                            if(initValue.indexOf("Set-0x0")>=0)
                                            {
                                                initValue = seKernel.ResolveStaticAddress(initValue).replace('\n',' ').trim();
                                            }
                                        }
                                        
                                        rtf.setText(initValue);
                                        //rtf.setText(ev.substring(ev.indexOf(":")+1));
                                        ggd.ComponentsObject = rtf;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            if(this.debug)
            {
                System.out.println("[INFO] about to fire graphic init events");
            }
            
            // Load Init labeled Graphics
            if(this._hasGraphics)
            {
                
                // loop through all components
                for(Enumeration en = seGraphics.elements(); en.hasMoreElements();)
                {
                    GraphicGuiDetails ggd = (GraphicGuiDetails) en.nextElement();
                    
                    if(ggd != null)
                    {
                        if(ggd.Events != null)
                        {
                            for(Enumeration em = ggd.Events.elements(); em.hasMoreElements();)
                            {
                                //if(ggd.GraphicsObject instanceof Bitmap)
                                {
                                    ev = (String) em.nextElement();
                                    if(ev.indexOf("Init")>=0)
                                    {
                                        //ggd.
                                        initValue = ev.substring(ev.indexOf(":")+1);
                                        
                                        // resolve static addresses
                                        if(this._hasStaticAddresses)
                                        {
                                            if(initValue.indexOf("Set-0x0")>=0)
                                            {
                                                initValue = seKernel.ResolveStaticAddress(initValue).replace('\n',' ').trim();
                                            }
                                        }
                                        
                                        ggd.HasText = true;
                                        ggd.ObjectText = initValue;
                                    }
                                }
                            }
                        }
                        else
                        {
                            if(ggd.Event !=null)
                            {
                                ev = (String) ggd.Event;
                                
                                if(ev.indexOf("Init")>=0)
                                {
                                    //ggd.
                                }
                            }
                        }
                    }
                }
            }
            
            if(this.debug)
            {
                System.out.println("[INFO] about to fire virtual stack init events");
            }
            
            // Load Start address location in proc and get to cyclin
            if(this._usesVirtualStack)
            {
                // check on Kernel object first
                if(this.seKernel == null)
                {
                    this.seKernel = new KernelExecutionDetails();
                }
                
                if(this.seKernel.CpuAlreadySetUp())
                {
                    // set up and Address Space
                    if(this.seKernel.SetUpCpuAddressSpace())
                    {
                        if(this.seKernel.SetUpCpuStack())
                        {
                            if(this.seKernel.CpuStarted())
                            {
                                _processorEngineStarted = true;
                                _addressMapInitialized = true;
                            }
                        }
                    }
                }
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems starting software engine:"+ex.toString());
            }
        }
        
        this.currentState = APP_ENGINE_STARTED;
    }
    
    public void stopEngine()
    {
        this.currentStatus = WAIT_FOR_ENGINE_STOP;
        
        
        this.currentStatus = APP_ENGINE_STOPPED;
    }
    
    public void loadLastState()
    {
        
    }
    
    public void saveCurrentState()
    {
        
    }
    
    public void setCurrentHttpCookie(String c)
    {
        this.currentHttpCookie = c;
    }
    
    public String getCurrentHttpCookie()
    {
        return this.currentHttpCookie;
    }
    
    public boolean appRequiresRemoteLogin()
    {
        
        return this._needsRemoteLogin;
    }

    public boolean appRequiresExternalEvent()
    {
        return this._needsExternalEvent;
    }
    
    public void appRequiresRemoteLogin(boolean l)
    {
        this._needsRemoteLogin = l;
    }
    
    public void applicationUserNPass(String u, String p)
    {
        this.user = u;
        this.pass = p;
    }

     public boolean appLoginSuccessful()
    {
        
        return this._loggedInSuccessfully;
    }
    
    public String getAppLoginPage()
    {
        
        return this.loginContentLocation;
    }

    public String getAppLoginPostData()
    {
        
        return this.loginPostContent;
    }


    public void setLoginPostContent(String content)
    {
        int startIndex = 0;
        int endIndex = 0;
        
        if(this.loginContentLocation.indexOf("aspx")>=0)
        {
            // set VIEWSTATE
            if(content.indexOf("<input type=\"hidden\" name=\"__VIEWSTATE")>=0)
            {
                startIndex = content.indexOf("<input type=\"hidden\" name=\"__VIEWSTATE");
                this.loginPostContent = "__VIEWSTATE=";
                
                if(content.indexOf("value=\"",startIndex)>=0)
                {
                    startIndex = content.indexOf("value=\"",startIndex)+7;
                    endIndex = content.indexOf("\" />",startIndex);
                
                    this.loginPostContent += islandSoftwareEngine.urlEncode(content.substring(startIndex,endIndex));
                }
                
            }
            
            this.loginPostContent += "&";
            
            // set EVENTVALIDATION
            if(content.indexOf("<input type=\"hidden\" name=\"__EVENTVALIDATION")>=0)
            {
                startIndex = content.indexOf("<input type=\"hidden\" name=\"__EVENTVALIDATION");
                this.loginPostContent += "__EVENTVALIDATION=";
                
                if(content.indexOf("value=\"",startIndex)>=0)
                {
                    startIndex = content.indexOf("value=\"",startIndex)+7;
                    endIndex = content.indexOf("\" />",startIndex);
                
                    this.loginPostContent += islandSoftwareEngine.urlEncode(content.substring(startIndex,endIndex));
                }
                
            }
            
            this.loginPostContent += "&";
            
            // UserName
            this.loginPostContent += "txtUsername=";
            this.loginPostContent += islandSoftwareEngine.urlEncode(this.user);
        
            this.loginPostContent += "&";
            
            // PassWord
            this.loginPostContent += "txtPassword=";
            this.loginPostContent += islandSoftwareEngine.urlEncode(this.pass);
        
            this.loginPostContent += "&";
            
            // PassWord
            this.loginPostContent += "btnLogin=";
            this.loginPostContent += "Login";
        }
        
        /*
        if(this.loginPostContent.length()>0)
        {
            this.loginPostContent = islandSoftwareEngine.urlEncode(loginPostContent);
        }
        */
    }
    
    //
    public void setLoginResultsPageContent(String Content)
    {
        // set content
        this.loginResultContent = Content;
      
        // fire content event
        this.PerformOperationForEvent("Loaded", this.loginLocationKey, Content);

        // set flag for others
        this._loggedInSuccessfully = true;
        
        // set current content location
        this.currentLocationKey = this.loginLocationKey;
    }
    
    public String getAppDownloadLocation()
    {
        return this.downloadContentLocation;
    }
    
    public int getAppTravelMethod()
    {
        int travelVia = 0;
        
        if(this.downloadTravelMethod.equals("Get"))
        {
            travelVia = islandTravelAgent.GET;
        }
        
        if(this.downloadTravelMethod.equals("Post"))
        {
            travelVia = islandTravelAgent.POST;
        }
        
        return travelVia;
    }

    public String getCurrentLocationKey()
    {
        return this.currentLocationKey;
    }

    public void setDownloadResultsContent(String Content)
    {
        if(Content.length()>0)
        {
            // set content
            this.downloadResultContent = Content;
            
            // fire content event
            this.PerformOperationForEvent("Loaded",this.downloadLocationKey,Content );
            
            // set flag for others
            this._eventFiredSuccessfully = true;
            
            // set current content location
            this.currentLocationKey = this.downloadLocationKey;
        }
    }
    
    /**
     * Function Loops through the guiMap and creates
     * relevant Graphic Gui objects that you can use to 
     * paint with. 
     * @return <description>
     */
    public Vector GetGraphicObjects()
    {   
        return seGraphics;
    }
    
    public Vector GetComponentObjects()
    {
        return seComponents;
    }

    public Vector GetContentObjects()
    {
        return seContent;
    }
    
    public Vector GetKernelObjects()
    {
        Vector kernelObs = null;
        
        return kernelObs;
    }

    public Vector GetLayoutObjects()
    {
        return seLayouts;
    }
    
    public Vector GetDisplayGrids()
    {
        return seGrids;
    }

    public void AddGuiObjectsToLayouts()
    {
        if(this._hasLayouts)
        {
            if(this._hasComponents)
            {
                for(Enumeration en = seComponents.elements(); en.hasMoreElements();)
                {
                    GraphicGuiDetails c = (GraphicGuiDetails) en.nextElement();
                    
                    if(c.Name.indexOf(".")>=0)
                    {
                        DisplayLayoutDetails l = (DisplayLayoutDetails) seLayouts.elementAt(Integer.parseInt(c.Name.substring(c.Name.indexOf(".")+1,c.Name.indexOf("]"))));
                    
                        c.BelongsToLayout = true;
                        c.LayoutIndx = l.indx;
                        l.AddGraphicGuiDetails(c);
                        
                        seComponents.setElementAt(c,c.indx);
                        seLayouts.setElementAt(l,l.indx);
                    }
                }
            }
            
            
            if(this._hasGraphics)
            {
                for(Enumeration en = seGraphics.elements(); en.hasMoreElements();)
                {
                    GraphicGuiDetails g = (GraphicGuiDetails) en.nextElement();
                    
                    if(g.Name.indexOf(".")>=0)
                    {
                        DisplayLayoutDetails l = (DisplayLayoutDetails) seLayouts.elementAt(Integer.parseInt(getGridLayoutId(g.Name.substring(g.Name.indexOf("."),g.Name.indexOf("]")))));
                    
                        g.LayoutIndx = l.indx;
                        g.BelongsToLayout = true;
                        l.AddGraphicGuiDetails(g);
                        
                        seGraphics.setElementAt(g,g.indx);
                        seLayouts.setElementAt(l,l.indx);
                        
                    }
                }
            }
        }
    }
    
    /**
     * Function takes the GUI map and gets the elements and
     * locations and places them correctly on the screen.
     * @param display <description>
     */
    public Manager AddGuiObjectsToManager()
    {
        Integer i;
        Object o;
        VerticalFieldManager displayComponentsScreen = null;
        
        if(this._hasComponents)
        {
            if(this.guiMap == null)
            {
                this.guiMap = new MultiMap();
            }
            
            displayComponentsScreen = new VerticalFieldManager();
            
            // draw Icons
            for(Enumeration keys = this.guiMap.keys(); keys.hasMoreElements();)
            {
                // get key
                i = (Integer) keys.nextElement();
                
                for(Enumeration v = this.guiMap.elements(i); v.hasMoreElements();)
                {
                    // get object
                    o = (Object) v.nextElement();
                
                    switch(i.intValue())
                    {
                        case islandSoftwareEngine.APP_GUI_PLACEMENT:
                            String place = (String) o;
                            
                            // put together a whole gui object
                            // and create
                            break;
                    }
                }
            }
            
            
            
            //displayScreen.add(displaySubScreen);
        }
        
        return displayComponentsScreen;
    }
    
    /**
     * Function takes the Resources in the gui map 
     * and fills in the details into the GraphicGuiDetails
     * object
     * @param display <description>
     */
    public void FillGraphicResourceDetail()
    {
        GraphicGuiDetails ggd = null; 
        Integer i;
        Object o;
        
        if(this._hasGraphics)
        {
            
            if(this.guiMap == null)
            {
                this.guiMap = new MultiMap();
            }
            
            // draw Icons
            for(Enumeration keys = this.guiMap.keys(); keys.hasMoreElements();)
            {
                // get key
                i = (Integer) keys.nextElement();
                
                for(Enumeration v = this.guiMap.elements(i); v.hasMoreElements();)
                {
                    // get object
                    o = (Object) v.nextElement();
                
                    switch(i.intValue())
                    {
                        case islandSoftwareEngine.APP_ICON_GRAPHIC:
                            try
                            {
                                
                                final Bitmap b_ig = (Bitmap) o;
                                if(b_ig!=null)
                                {
                                   //ggd = this.new GraphicGuiDetails();
                                   
                                   //ggd.
                                }
                                
                            }
                            catch(Exception e)
                            {
                                if(this.debug)
                                {
                                    System.out.println("[ERROR] problems adding graphics object:"+e.toString());
                                }
                            }
                            break;
                    
                        case islandSoftwareEngine.APP_BACKGROUND_GRAPHIC:
                            try
                            {
                                
                                final Bitmap b_bg = (Bitmap) o;
                                if(b_bg!=null)
                                {
                                    
                                }
                                
                            }
                            catch(Exception ex)
                            {
                                if(this.debug)
                                {
                                    System.out.println("[ERROR] problems adding graphics objects:"+ex.toString());
                                }
                            }
                            break;
                    }
                }
            }
        }
    }

    public void SetTranslatorPoints(MultiMap points)
    {
        this.translatorPoints = points;
    }
    
    public void SetGuiMap(Hashtable h)
    {
        if(this.guiMap == null)
        {
            this.guiMap = new MultiMap();
        }
        
        for (Enumeration keyContainer = h.keys(); keyContainer.hasMoreElements();) 
        {
            this.guiMap.add(keyContainer.nextElement(), h.get(keyContainer.nextElement()));
        }
    } 

    public void SetLayoutMap(Hashtable h)
    {
        if(this.layoutMap == null)
        {
            this.layoutMap = new MultiMap();
        }
        
        for (Enumeration keyContainer = h.keys(); keyContainer.hasMoreElements();) 
        {
            this.layoutMap.add(keyContainer.nextElement(), h.get(keyContainer.nextElement()));
        }
    } 

    public void SetGuiMapElements(Hashtable h)
    {
        if(this.guiMap == null)
        {
            this.guiMap = new MultiMap();
        }
        
        for (Enumeration keyContainer = h.keys(); keyContainer.hasMoreElements();) 
        {
            this.guiMap.add(keyContainer.nextElement(), h.get(keyContainer.nextElement()));
        }
    } 

    public void SetLayoutMapElements(Hashtable h)
    {
        if(this.layoutMap == null)
        {
            this.layoutMap = new MultiMap();
        }
        
        for (Enumeration keyContainer = h.keys(); keyContainer.hasMoreElements();) 
        {
            this.layoutMap.add(keyContainer.nextElement(), h.get(keyContainer.nextElement()));
        }
    } 

    public void SetGuiMapElements(MultiMap m)
    {
        if(this.guiMap == null)
        {
            this.guiMap = new MultiMap();
        }
        
        for (Enumeration keyContainer = m.keys(); keyContainer.hasMoreElements();) 
        {
            for(Enumeration valueContainer = m.elements(keyContainer); valueContainer.hasMoreElements();)
            {
                this.guiMap.add(keyContainer.nextElement(), valueContainer.nextElement());
            }
        }
    } 
    
    public void SetLayoutMapElements(MultiMap m)
    {
        if(this.layoutMap == null)
        {
            this.layoutMap = new MultiMap();
        }
        
        for (Enumeration keyContainer = m.keys(); keyContainer.hasMoreElements();) 
        {
            for(Enumeration valueContainer = m.elements(keyContainer); valueContainer.hasMoreElements();)
            {
                this.layoutMap.add(keyContainer.nextElement(), valueContainer.nextElement());
            }
        }
    } 
    
    /**
     * Function provides a way to add just some content
     * elements to the internal translatorMap and resourcePoints
     * @param obs <description>
     * @param elementType <description>
     */
    public void SetContentMapElements(Enumeration obs, int elementType)
    {
        islandApplicationResource iar;
        StringBuffer contentData;
        Hashtable objIds;
        ContentManagementDetails cmd = null;
        String objId;
        String key;
        Object obj;
        int type;
        int vIndx;
        
        
        if(this.translatorPoints == null)
        {
            this.translatorPoints = new MultiMap();
        }
        
        try
        {
            
            // loop through these elements for dispersion
            for (Enumeration elementContainer = obs; elementContainer.hasMoreElements();) 
            {
                obj = (Object) elementContainer.nextElement();
                    
                // switch?
                switch(elementType)
                {
                    case islandSoftwareEngine.APP_CONTENT_EVENT:
                        
                        key = (String) obj;
                        if(key.indexOf("Login")>=0)
                        {
                            setLoginDetails(key);
                        }
                        else
                        // we'll need an object
                        {
                            // find corresponding graphic and set  BackgroundImage[1]_x:y 
                            //key = (String)obj;
                                
                            cmd = new ContentManagementDetails();
                            cmd.Event = getContentEvent(key);
                            cmd.Id = getContentId(key);
                            cmd.Name = getContentName(key);
                            cmd.Content = getContentValue(key);
                            cmd.SetEventDetails();
                            
                            if(seContent.size()<=Integer.valueOf(cmd.Id).intValue())
                            {
                                // backfill with nulls
                                for(int a=0; a<Integer.valueOf(cmd.Id).intValue()+1; a++)
                                {
                                    seContent.addElement(null);
                                            
                                    if(seContent.size()>Integer.valueOf(cmd.Id).intValue())
                                    {
                                        break;
                                    }
                                }
                             }
                        
                             obj = (Object) cmd;
                            
                             // update the object
                             if(seContent.elementAt(Integer.valueOf(cmd.Id).intValue())==null)
                             {
                                seContent.insertElementAt(cmd,Integer.valueOf(cmd.Id).intValue());
                             }
                             else
                             {
                                seContent.addElement(cmd);
                             }
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type APP_CONTENT_EVENT");
                        } 
                        
                        break;
                    case islandSoftwareEngine.APP_CONTENT_MANAGEMENT:
                        
                        // check private variables for needed management 
                        //if(obj instanceof java.lang.String)
                        //{
                            key = (String) obj;
                                        
                            if(this.loginManagementKey !=null)
                            {
                                if(this.loginManagementKey.length() > 0)
                                {
                                        key = (String) obj;
                                        
                                        if(key.indexOf(this.loginManagementKey)>=0)
                                        {
                                            if(key.indexOf("Location")>=0)
                                            {
                                                // set management location
                                                this.loginContentLocation = key.substring(key.indexOf(":")+1);
                                                this.loginLocationKey = this.loginContentLocation;
                                            }
                                        }
                                 }
                            }
                        //}
                        
                        if(key.indexOf(this.loginManagementKey)<0)
                        {
                            // find corresponding graphic and set  BackgroundImage[1]_x:y 
                            vIndx = this.appContentExists(obj);
                                    
                            // if it already exists
                            if(vIndx>=0)
                            {
                                if(obj instanceof ContentManagementDetails)
                                {
                                    key = cmd.Name;
                                    
                                    cmd = (ContentManagementDetails) seContent.elementAt(vIndx);
                                    cmd.SetManagementDetails(key);                                        
                                            
                                    // update the object
                                    seContent.setElementAt(cmd,vIndx);
                                        
                                    obj = (Object) cmd;
                                
                                }
                                
                                // do the one
                                if(obj instanceof String)
                                {
                                    key = (String)obj;
                                        
                                    cmd = (ContentManagementDetails) seContent.elementAt(vIndx);
                                    cmd.SetManagementDetails(key);                                        
                                            
                                    // update the object
                                    seContent.setElementAt(cmd,vIndx);
                                        
                                    obj = (Object) cmd;
                                }
                                
                                
                                
                                // look for others
                                for(int i = 0; i<seContent.size();i++)
                                {
                                    cmd = (ContentManagementDetails) seContent.elementAt(i);
                                    
                                    if(cmd!=null)
                                    {
                                        // if the id's match then
                                        if(cmd.Id.equals(getContentId(key)))
                                        {
                                            cmd.SetManagementDetails(key);  
                                            
                                            // update the object
                                            seContent.setElementAt(cmd,i);
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if(obj instanceof java.lang.String)
                                {
                                    key = (String) obj;
                                    cmd = new ContentManagementDetails();
                                    cmd.Name = getContentName(key);
                                    cmd.Id = getContentId(key);
                                    cmd.SetEventDetails();
                                    cmd.SetManagementDetails(key);
                                    
                                    // update the object
                                    if(seContent.elementAt(Integer.valueOf(cmd.Id).intValue())==null)
                                    {
                                        seContent.insertElementAt(cmd,Integer.valueOf(cmd.Id).intValue());
                                    }
                                    else
                                    {
                                        seContent.addElement(cmd);
                                    }
                                    
                                    obj = (Object) cmd;
                                }
                            }                                
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type APP_CONTENT_MANAGEMENT");
                        }    
                        break;
                }
                
                // save to gui map as graphic placement & event
                this.translatorPoints.add(new Integer(elementType),obj);
    
             }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems updating translatorMap with content:"+e.toString());
                e.printStackTrace();
            }
        }
    }
    
    /**
     * Function provides a way to add just some content
     * elements to the internal translatorMap and resourcePoints
     * @param obs <description>
     * @param elementType <description>
     */
    public void SetKernelMapElements(Enumeration obs, int elementType)
    {
        islandApplicationResource iar;
        StringBuffer coreData;
        Hashtable objIds;
        Hashtable memoryData;
        String objId;
        String key;
        Object obj;
        int type;
        int vIndx;
        
        
        if(this.translatorPoints == null)
        {
            this.translatorPoints = new MultiMap();
        }
        
        try
        {
            
            // loop through these elements for dispersion
            for (Enumeration elementContainer = obs; elementContainer.hasMoreElements();) 
            {
                obj = (Object) elementContainer.nextElement();
                    
                // switch?
                switch(elementType)
                {
                    case islandSoftwareEngine.APP_VENDOR_LIB:
                        key = (String) obj;
                        
                        if((key.indexOf("Set-0x")>=0)&&(key.indexOf("lib")>=0))
                        {
                            seKernel.AddVendorLibrary(key.substring(0,key.indexOf("-lib")),key.substring(key.indexOf("-lib")+4,key.indexOf("_")));
                        }
                        
                        if(seKernel.Libs==null)
                        {
                            seKernel.Libs = new Vector();
                        }
                        
                        if(seKernel.Addresses==null)
                        {
                            seKernel.Addresses = new Hashtable();
                        }
                        
                        if(key.indexOf("Set-0x000000")>=0)
                        {
                            // no address listing
                            seKernel.Libs.addElement(key.substring(key.indexOf("lib")+3,key.indexOf("_")));
                        }
                        else
                        {
                            seKernel.Libs.addElement(key.substring(key.indexOf("lib")+3,key.indexOf("_")));
                            seKernel.Addresses.put(key.substring(key.indexOf("Set-0x")+6),key.substring(key.indexOf("lib"),key.indexOf("_")));
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type APP_VENDOR_LIB");
                        }
                        break;
                    case islandSoftwareEngine.APP_API_FRAMEWORK:
                    
                        key = (String) obj;
                        
                        if(key !=null)
                        {
                            // Make sure it really is an api Mapping
                            if(key.indexOf("API")>=0)
                            {
                                // get api name w/ index
                                String apiNameNKey = key.substring(key.indexOf("API-")+4,key.indexOf("_Set"));
                                
                                // get library name w/ index
                                String libNameNKey = key.substring(key.indexOf(":")+1);
                                
                                // get Address value
                                String addressLoc = key.substring(key.indexOf("Set-")+4,key.indexOf(":"));
                                
                                try
                                {
                                    // save API
                                    seKernel.AddFramework(apiNameNKey);
                                    
                                    // save Address for API
                                    seKernel.Addresses.put(apiNameNKey,addressLoc);
                                    
                                    // save libs to framwork entry hashtable
                                    if(seKernel.ApiLibs==null)
                                    {
                                        seKernel.ApiLibs = new Hashtable();
                                    }
                                    
                                    // try to get existing list
                                    Vector libsForApi = (Vector) seKernel.ApiLibs.get(apiNameNKey);
                                    
                                    if(libsForApi!=null)
                                    {
                                        if(!libsForApi.contains(libNameNKey))
                                        {
                                            libsForApi.addElement(libNameNKey);
                                        }
                                    }
                                    else
                                    {
                                        libsForApi = new Vector();
                                        
                                        libsForApi.addElement(libNameNKey);
                                    }
                                    
                                    // insert or replace API libs updated
                                    seKernel.ApiLibs.put(apiNameNKey,libsForApi);
                                }
                                catch(Exception ex)
                                {
                                     if(this.debug)
                                     {
                                        System.out.println("[ERROR] problems adding Framework reference to kernel:"+ex.toString());
                                     }
                                }     
                            }
                        }
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type APP_API_FRAMEWORK");
                        }
                        break;
                    case islandSoftwareEngine.APP_API_FUNCTION:
                        key = (String) obj;
                        boolean isLib = false;
                        
                        if(key !=null)
                        {
                            if(key.indexOf("lib")>=0)
                            {
                                isLib = true;
                            }
                            
                            try
                            {
                                String functionNameNKey = key.substring(key.indexOf("]-")+2,key.indexOf("_Set"));
                                seKernel.Function = functionNameNKey;
                                
                                String frameworkNameNKey = key.substring(0,key.indexOf("]-")+1);
                                
                                String addressLocation = key.substring(key.indexOf("0x")+2);
                                seKernel.Address = addressLocation;
                                
                                // add address for function
                                seKernel.AddFunctionAddress(elementType);
                                
                                if(!isLib)
                                {
                                    // save functions to framework entry table
                                    if(seKernel.ApiFunctions==null)
                                    {
                                        seKernel.ApiFunctions = new Hashtable();
                                    }
                                            
                                    // try to get existing list
                                    Vector functionsForApi = (Vector) seKernel.ApiFunctions.get(frameworkNameNKey);
                                            
                                    if(functionsForApi!=null)
                                    {
                                        if(!functionsForApi.contains(frameworkNameNKey))
                                        {
                                            functionsForApi.addElement(frameworkNameNKey);
                                        }
                                    }
                                    else
                                    {
                                        functionsForApi = new Vector();
                                                
                                        functionsForApi.addElement(frameworkNameNKey);
                                    }
                                            
                                    // insert or replace API functions updated
                                    seKernel.ApiFunctions.put(frameworkNameNKey,functionsForApi);
                                }
                                else
                                {
                                    // save functions to framework entry table
                                    if(seKernel.LibFunctions==null)
                                    {
                                        seKernel.LibFunctions = new Hashtable();
                                    }
                                            
                                    // try to get existing list
                                    Vector functionsForLib = (Vector) seKernel.ApiFunctions.get(frameworkNameNKey);
                                            
                                    if(functionsForLib!=null)
                                    {
                                        if(!functionsForLib.contains(frameworkNameNKey))
                                        {
                                            functionsForLib.addElement(frameworkNameNKey);
                                        }
                                    }
                                    else
                                    {
                                        functionsForLib = new Vector();
                                                
                                        functionsForLib.addElement(frameworkNameNKey);
                                    }
                                            
                                    // insert or replace API functions updated
                                    seKernel.ApiFunctions.put(frameworkNameNKey,functionsForLib);
                                }
                            }
                            catch(Exception e)
                            {
                                if(this.debug)
                                {
                                    System.out.println("[ERROR] problems adding framework function:"+e.toString());
                                }
                            }
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type APP_API_FUNCTION");
                        }
                        break;
                    case islandProcessorEngine.PROCESSOR_KERNEL_FUNCTION:
                        
                    
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type PROCESSOR_KERNEL_FUNCTION");
                        }
                        break;
                     case islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION:
                        key = (String) obj;
                        
                        if(key !=null)
                        {
                            if(key.indexOf("Set-0x")>=0)
                            {
                                try
                                {
                                    String addressLocation = key.substring(key.indexOf("Set-0x")+6);
                                    seKernel.Address = addressLocation;
                                    
                                    String functionNameNKey = key.substring(0,key.indexOf("Set-0x"));
                                    seKernel.Function = functionNameNKey;
                                    
                                    seKernel.AddFunctionAddress(elementType);
                                    
                                }
                                catch(Exception ec)
                                {
                                    if(this.debug)
                                    {
                                        System.out.println("[ERROR] problems adding local proc function properties"+ec.toString());
                                    }
                                }
                            }
                        }
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type PROCESSOR_LOCAL_FUNCTION");
                        }
                        break;
                    case islandSoftwareEngine.KERNEL_CPU:
                        key = (String) obj;
                        
                        if(key !=null)
                        {
                            if(key.indexOf(":Execute")>=0)
                            {
                                try
                                {
                                    String operationNameNKey = key.substring(0,key.indexOf("_"));
                                    String procName = key.substring(key.indexOf("_")+1,key.indexOf(":"));
                                    String runLocation = key.substring(key.indexOf("-")+1);
                                    
                                    // get address for runLocation
                                    String address = seKernel.GetProgramAddress("",runLocation, elementType);
                                    
                                    // get proc Type for name
                                    int mType = seKernel.GetCPUTypeFromName(procName); 
                                    
                                    // process instructions starting at location to Value, stack event or IRQ/SWI
                                    seKernel.ProcessInstructions(address,mType);
                                }
                                catch(Exception e)
                                {
                                    if(this.debug)
                                    {
                                        System.out.println("[ERROR] problems setting up Kernel cpu action");
                                    }
                                }
                            }
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type KERNEL_CPU");
                        }
                        break;
                    case islandSoftwareEngine.KERNEL_IO:
                        key = (String) obj;
                        
                        try
                        {
                            if(key.indexOf("From")>=0)
                            {
                                String addressLabel = key.substring(key.indexOf("From-")+6,key.indexOf("_"));
                                String objectName = key.substring(key.indexOf("_")+1);
                                
                                seKernel.WriteAddressFromObject(objectName,addressLabel);
                            
                                memoryData = new Hashtable();
                                memoryData.put(objectName,"From-"+addressLabel);
                                obj = (Object) memoryData;
                            }
                            
                            if(key.indexOf("To")>=0)
                            {
                                String addressLabel = key.substring(key.indexOf("To-")+3,key.indexOf("_"));
                                String objectName = key.substring(key.indexOf("_")+1);
                                
                                seKernel.WriteObjectFromAddress(objectName,addressLabel);
                            
                                memoryData = new Hashtable();
                                memoryData.put(objectName,"To-"+addressLabel);
                                obj = (Object) memoryData;
                            }
                        }
                        catch(Exception ex)
                        {
                            if(this.debug)
                            {
                                System.out.println("[ERROR] problems creating IO objects for Kernel:"+ex.toString());
                            }
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type KERNEL_IO");
                        }
                        break;
                    case islandSoftwareEngine.KERNEL_IRQ:
                        
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type KERNEL_IRQ");
                        }
                        break;
                    case islandSoftwareEngine.KERNEL_MEMORY:
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type KERNEL_MEMORY");
                        }
                        break;
                    case islandSoftwareEngine.KERNEL_MULTITASKTING:
                        break;
                    case islandSoftwareEngine.KERNEL_NETWORK:
                        
                        // look for content
                        if(this.loginContentLocation !=null)
                        {
                            if(this.loginContentLocation.length() > 0)
                            {
                                if(obj instanceof java.lang.String)
                                {
                                    key = (String) obj;
                                    
                                    if(key.indexOf(this.loginContentLocation)>=0)
                                    {
                                        // update page location
                                        this.loginContentLocation = getKernelNetworkLocation(key);
                                    }
                                    else
                                    {
                                        getKernelNetworkLocation(key);    
                                    }
                                }
                            }
                        }
                        else
                        {
                            if(obj instanceof java.lang.String)
                            {
                                key = (String) obj;
                                
                                getKernelNetworkLocation(key);
                            }
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("[INFO] element is of type KERNEL_NETWORK");
                        }
                        break;
               
                }    
                
                // save to translator bucket as kernel functoin & event
                this.translatorPoints.add(new Integer(elementType),obj);
            }
       }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems updating translatorMap with kernel calls:"+e.toString());
            }
        }
    }
    
    public void SetAddressMapElements(Enumeration obs, int elementType)
    {
        islandApplicationResource iar;
        Hashtable objIds;
        Hashtable memoryAddress;
        String objId;
        String key;
        Object obj;
        byte[] img;
        int type;
        int vIndx;
            
        if(this.kernelMap == null)
        {
            this.kernelMap = new MultiMap();
        }   
        
        if(obs == null)
        {
            obs = new EmptyEnumeration();
        }
        
        try
        {
            if(elementType == islandApplicationResource.FILE)
            {
                // initializations
                obj = null;
                type = elementType;
             
                try
                {
                    for(Enumeration element = obs; element.hasMoreElements();)
                    {
                        obj = null;
            
                        // packed resources list
                        iar = (islandApplicationResource) element.nextElement();
                                
                        // add contents to guiMap
                        for(Enumeration keys = iar.getAddressKeys(); keys.hasMoreElements();)
                        {
                            obj = (Object)keys.nextElement();
                            
                            if(obj instanceof String)
                            {
                                key = (String)obj;
                                
                                if(this.debug)
                                {
                                    //System.out.println("[INFO] key is :"+key);
                                }
                            
                                type = islandSoftwareEngine.KERNEL_MEMORY; 
                                
                                switch(type)
                                {
                                    case islandSoftwareEngine.KERNEL_MEMORY:
                                        
                                        if(iar.getAddressData(key) instanceof Hashtable)
                                        {
                                            if(key.equals("CString"))
                                            {
                                                // set static address list/lookup table
                                                seKernel.CStringAddresses = (Hashtable) iar.getAddressData(key);
                                                this._hasStaticAddresses = true;
                                            }
                                        }
                                    break;
                                }
                            }
                        }
                    }
                }
                catch(Exception e)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems updating kernelMap from Addresses file download:"+e.toString());
                    }
                }
            }
            else
            {
                try
                {
                    // loop through these elements for dispersion
                    for (Enumeration elementContainer = obs; elementContainer.hasMoreElements();) 
                    {
                        obj = (Object) elementContainer.nextElement();
                        
                        
                            // switch?
                            switch(elementType)
                            {
                                case islandSoftwareEngine.APP_STATIC_ADDRESS:
                                    break;
                                case islandSoftwareEngine.KERNEL_MEMORY:
                                    try
                                    {
                                        if(obj!=null)
                                        {
                                            key = (String)obj;
                                        
                                            if(key.indexOf("_")>=0)
                                            {
                                                if(key.indexOf("Set[")>=0)
                                                {
                                                    String label = key.substring(0,key.indexOf("_"));
                                                    String addyId = key.substring(key.indexOf("["));
                                                    
                                                    // updating or adding memory value
                                                    seKernel.SetProgramAddress(label,addyId,elementType);
                                                
                                                    // set kernel Map
                                                    memoryAddress = new Hashtable();
                                                    memoryAddress.put(label,addyId);
                                                    obj = (Object) memoryAddress;
                                                }
                                                
                                                if((key.indexOf("Get[")>=0))
                                                {
                                                    String variable = key.substring(0,key.indexOf("_"));
                                                    String label = key.substring(key.indexOf("["));
                                                    
                                                    
                                                    // getting or removing memory value
                                                    String addyId = seKernel.GetProgramAddress(variable, label,elementType);
                                                
                                                    // set kernel Map
                                                    memoryAddress = new Hashtable();
                                                    memoryAddress.put(variable,addyId);
                                                    obj = (Object) memoryAddress;
                                                }
                                                
                                                if((key.indexOf("To-")>=0))
                                                {
                                                    String variable = key.substring(0,key.indexOf("_"));
                                                    String addressLabel = key.substring(key.indexOf("To-")+3);
                                                    
                                                    // write value To memory
                                                    seKernel.WriteValueToAddress(variable, addressLabel,elementType);
                                                
                                                    // set kernel Map
                                                    memoryAddress = new Hashtable();
                                                    memoryAddress.put(variable,addressLabel);
                                                    obj = (Object) memoryAddress;
                                                }
                                                
                                                if((key.indexOf("From-")>=0))
                                                {
                                                    String variable = key.substring(0,key.indexOf("_"));
                                                    String addressLabel = key.substring(key.indexOf("From-")+5);
                                                    
                                                    
                                                    // read value from memory
                                                    String addyValue = seKernel.ReadValueFromAddress(addressLabel,elementType);
                                                
                                                    // set kernel Map
                                                    memoryAddress = new Hashtable();
                                                    memoryAddress.put(variable,addyValue);
                                                    obj = (Object) memoryAddress;
                                                }
                                            }
                                        }
                                    }
                                    catch(Exception ex)
                                    {
                                        if(this.debug)
                                        {
                                            System.out.println("[ERROR] problems adding KERNEL_MEMORY address or value");
                                        }
                                    }
                                    
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] element is of type KERNEL_MEMORY");
                                    }
                                    break;
                                case islandSoftwareEngine.KERNEL_IO:
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] element is of type KERNEL_IO");
                                    }
                                    break;
                                case islandProcessorEngine.PROCESSOR_KERNEL_FUNCTION:
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] element is of Type PROCESSOR_KERNEL_FRAMEWORK");
                                    }
                                    break;
                                case islandSoftwareEngine.APP_API_FRAMEWORK:
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] element is of Type APP_API_FRAMEWORK");
                                    }
                                    break;
                                case islandSoftwareEngine.APP_API_FUNCTION:
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] element is of Type APP_API_FUNCTION");
                                    }
                                    break;
                                case islandProcessorEngine.PROCESSOR_LOCAL_FUNCTION:
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] element is of type PROCESSOR_LOCAL_FUNCTION");
                                    }
                                    break;
                            }
                            
                        this.kernelMap.add(new Integer(elementType),obj);
                    }        
                }
                catch(Exception e)
                {
                    if(this.debug)
                    {
                        System.out.println("[ERROR] problems updating kernelMap from Addresses Multimap category"+e.toString());
                    }
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                    System.out.println("[ERROR] problems updating kernelMap with Addresses:"+e.toString());
            }

        }            
    }
    
    public void SetOperationMapElements(Enumeration obs, int elementType)
    {
        islandApplicationResource iar;
        Hashtable objIds;
        String objId;
        String key;
        Object obj;
        byte[] img;
        int type;
        int kType;
        int vIndx;
        
        if(this.kernelMap == null)
        {
            this.kernelMap = new MultiMap();
        }   
        
        
        try
        {
            if(elementType == islandApplicationResource.FILE)
            {
                // initializations
                obj = null;
                type = elementType;
                kType = islandSoftwareEngine.KERNEL_CPU; 
                                
                try
                {
                    if(this.debug)
                    {
                        System.out.println("[INFO] setting Processor Operations from download FILE");
                    }
                    
                    for(Enumeration element = obs; element.hasMoreElements();)
                    {
                        obj = (Object)element.nextElement();
                
                        if(obj instanceof islandApplicationResource)
                        {
                            // packed resources list
                            iar = (islandApplicationResource) obj;
                                        
                            // add contents to guiMap
                            for(Enumeration keys = iar.getOperationKeys(); keys.hasMoreElements();)
                            {
                                Object keyOb = keys.nextElement();
                                
                                if(keyOb instanceof String)
                                {
                                    key = (String)keyOb;
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] key is :->"+key);
                                    }
                            
                                    if(key.indexOf("ARMv")>=0)
                                    {
                                    
                                        type = islandProcessorEngine.PROCESSOR_ARM_ARCH;
                                    }
                                    
                                    if(key.indexOf("BFINv")>=0)
                                    {
                                    
                                        type = islandProcessorEngine.PROCESSOR_BFIN_ARCH;
                                    }
        
                                    if(key.indexOf("COLDFIREv")>=0)
                                    {
                                    
                                        type = islandProcessorEngine.PROCESSOR_COLDFIRE_ARCH;
                                    }
                                        
                                    if(key.indexOf("M86v")>=0)
                                    {
                                    
                                        type = islandProcessorEngine.PROCESSOR_M86_ARCH;
                                    }
                                    
                                    if(key.indexOf("MIPSv")>=0)
                                    {
                                    
                                        type = islandProcessorEngine.PROCESSOR_MIPS_ARCH;
                                    }
                                    
                                    if(key.indexOf("PPCv")>=0)
                                    {
                                    
                                        type = islandProcessorEngine.PROCESSOR_PPC_ARCH;
                                    }
                                    
                                    if(key.indexOf("SPARCv")>=0)
                                    {
                                    
                                        type = islandProcessorEngine.PROCESSOR_SPARC_ARCH;
                                    }
                                    
                                    if(key.indexOf("x86v")>=0)
                                    {
                                    
                                        type = islandProcessorEngine.PROCESSOR_x86_ARCH;
                                    }
                                    
                                    
                                    switch(type)
                                    {
                                        case islandProcessorEngine.PROCESSOR_ARM_ARCH:
                                            
                                            if(key.indexOf(":MMU")>=0)
                                            {
                                                seKernel.SetUpCpu(type,true); 
                                            }
                                            else
                                            {
                                                seKernel.SetUpCpu(type,false);
                                            }
                                            
                                            if(iar.getOperationData(key) instanceof Hashtable)
                                            {
                                                Hashtable operations = (Hashtable) iar.getOperationData(key);
                                                
                                                // loop through the addresses and add them to the cpu
                                                for(Enumeration enu = ((Hashtable)iar.getOperationData(key)).keys(); enu.hasMoreElements();)
                                                {
                                                    String operationAddress = (String) enu.nextElement();
                                                    String operationInstruction =  (String)operations.get(operationAddress);
                                                    
                                                    seKernel.AddCpuAddress(operationAddress, operationInstruction, type);
                                                }
                                            }
                                            
                                            obj = (islandProcessorEngine) seKernel.Cpu;
                                            
                                            this.kernelMap.add(new Integer(kType),obj);
                                            
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type PROCESSOR_ARM_ARCH");
                                            }
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
                catch(Exception e)
                {
                    if(this.debug)
                    {
                       System.out.println("[ERROR] problems adding processor operations to kernelMap:"+e.toString()); 
                    }
                }
            }
            else
            {
                try
                {
                    // loop through these elements for dispersion
                    for (Enumeration elementContainer = obs; elementContainer.hasMoreElements();) 
                    {
                        obj = (Object) elementContainer.nextElement();
                        
                        switch(elementType)
                        {
                            case islandProcessorEngine.PROCESSOR_ARCHS:
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type PROCESSOR_ARCHS");
                                }
                                break;
                            case islandProcessorEngine.PROCESSOR_PROGRAM_COUNTER_EVENT:
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type PROCESSOR_PROGRAM_COUNTER_EVENT");
                                }
                                break;
                            case islandProcessorEngine.PROCESSOR_LINK_EVENT:
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type PROCESSOR_LINK_EVENT");
                                }
                                break;
                            case islandProcessorEngine.PROCESSOR_STACK_EVENT:
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type PROCESSOR_STACK_EVENT");
                                }
                                break;
                            case islandProcessorEngine.PROCESSOR_OPERATIONS:
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type PROCESSOR_OPERATIONS");
                                }
                                break;
                        }
                    }
                }
                catch(Exception e)
                {
                    if(this.debug)
                    {
                       System.out.println("[ERROR] problems adding processor operations to kernelMap"); 
                    }
                }
            }
        }
        catch(Exception ex)
        {
            
        }
    }
    
    /**
     * Function provides a way to add just some gui
     * elements to the internal guiMap and resourcePoints
     * @param obs <description>
     * @param elementType <description>
     */
    public void SetGuiMapElements(Enumeration obs, int elementType)
    {
        islandApplicationResource iar;
        DataBuffer imgData;
        Hashtable objIds;
        GraphicGuiDetails ggd;
        DisplayLayoutDetails dld;
        GridScreenDetails gsd;
        String objId;
        String key;
        Integer intKey;
        Bitmap bmp; 
        Object obj;
        byte[] img;
        int type;
        int vIndx;
        
        int imgHt = 0;
        int imgWidth = 0;
        Object imgUrl = null;
        String mediaName = "";
        int mediaCount = 0;

        if(this.guiMap == null)
        {
            this.guiMap = new MultiMap();
        }   
        
        if(elementType == islandApplicationResource.FILE)
        {
            // initializations
            obj = null;
            type = elementType;
             
            try
            {
                // Gui stuffs
                for(Enumeration element = obs; element.hasMoreElements();)
                {
                    obj = null;
            
                    // packed resources list
                    iar = (islandApplicationResource) element.nextElement();
                                
                    // add contents to guiMap
                    for(Enumeration keys = iar.getGuiResourceKeys(); keys.hasMoreElements();)
                    {
                        key = (String)keys.nextElement();
                        if(this.debug)
                        {
                            System.out.println("[INFO] key is :->"+key);
                        }
                        
                        // one to one mappings
                        if(key.equals("Icon")||key.equals("BackgroundImage"))
                        {
                            if(key.equals("Icon"))
                            {
                                type = islandSoftwareEngine.APP_ICON_GRAPHIC; 
                                this._hasGraphics = true;
                            }
    
                            if(key.equals("BackgroundImage"))
                            {
                                type = islandSoftwareEngine.APP_BACKGROUND_GRAPHIC; 
                                this._hasGraphics = true;
                            }
                        
                            switch(type)
                            {
                                case islandSoftwareEngine.APP_ICON_GRAPHIC:
                                    // create a bitmap from data
                                    if(iar.getGuiResourceData(key) instanceof DataBuffer)
                                    {
                                        
                                        imgData = (DataBuffer) iar.getGuiResourceData(key);
                                        img = imgData.toArray();
                                        bmp = Bitmap.createBitmapFromPNG(img ,0,img.length);
                                        ggd = this.new GraphicGuiDetails(); 
                                        ggd.Name = "Icon_Image";
                                        ggd.GraphicsObject = bmp;
                                        ggd.Id = "0";
                                        obj = (Object) ggd;
                                        //seGraphics.addElement(obj);
                                        
                                        seGraphics.insertElementAt(obj,0);
                                        
                                        this._hasGraphics = true;
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] resourceData is resourceData");
                                        }
                                    }
                                    else
                                    {
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] resourceData is resourceLocation");
                                        }
                                    }
                                    
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] element is of type APP_ICON_GRAPHIC");
                                    }
                                    break;
                                case islandSoftwareEngine.APP_BACKGROUND_GRAPHIC:
                                    // create a bitmap from data
                                    if(iar.getGuiResourceData(key) instanceof DataBuffer)
                                    {
                                        
                                        imgData = (DataBuffer) iar.getGuiResourceData(key);
                                        img = imgData.getArray();
                                        bmp = Bitmap.createBitmapFromPNG(img ,0,img.length);
                                        ggd = this.new GraphicGuiDetails(); 
                                        ggd.Name = "Background_Image";
                                        ggd.GraphicsObject = bmp;
                                        ggd.Id = "1"; // first and probably only background image
                                        obj = (Object) ggd;
                                        
                                        if(seGraphics.size()<=1)
                                        {
                                            seGraphics.addElement(null);
                                        }
                                        
                                        //seGraphics.addElement(obj);
                                        seGraphics.insertElementAt(obj, 1);
                                        this._hasGraphics = true;
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] resourceData is resourceData");
                                        }
                                       
                                    }
                                    else
                                    {
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] resourceData is resourceLocation");
                                        }
                                    }
                                    
                                    if(this.debug)
                                    {   
                                        System.out.println("[INFO] element is of type APP_BACKGROUND_GRAPHIC");
                                    }
                                    break;
                            }
                                
                            this.guiMap.add(new Integer(type), obj);
                        }
                        else
                        {
                            // one to many mappings
                            if(key.equals("BmpImages"))
                            {
                                // Hashtable of random is keys to Bmp objects
                                type = islandSoftwareEngine.APP_BMP_GRAPHIC;
                                objIds = (Hashtable) iar.getGuiResourceData(key);
                                this._hasGraphics = true;
                                
                                for(Enumeration keyObjIds = objIds.keys(); keyObjIds.hasMoreElements();)
                                {
                                    try
                                    {
                                        
                                        // Get the key put it in ObjId and create the Details object
                                        objId = (String) keyObjIds.nextElement();
                                        imgData = (DataBuffer) objIds.get(objId);
                                        img = imgData.getArray();
                                        bmp = Bitmap.createBitmapFromBytes(img ,0,img.length,1);
                                        ggd = this.new GraphicGuiDetails();
                                        ggd.Name = getGuiGraphicName(objId); // name[id]
                                        ggd.GraphicsObject = bmp; // obj
                                        ggd.Id = getGuiId(objId); // order number of bmp image 
                                        obj = (Object) ggd;
                                        //seGraphics.addElement(obj); // add to vector
                                        
                                        if(seGraphics.size() == 0)
                                        {
                                            seGraphics.addElement(null); // blank Icon
                                            seGraphics.addElement(null); // Blank BackgroundImage
                                        }
                                        
                                        if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                        {
                                                // backfill with nulls
                                                for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                {
                                                    seGraphics.addElement(null);
                                                    
                                                    if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                    {
                                                        break;
                                                    }
                                                }
                                        }
                                
                                        seGraphics.insertElementAt(obj,Integer.valueOf(ggd.Id).intValue());
                                        
                                        this.guiMap.add(new Integer(type), obj); // add to type map
                                        
                                    }
                                    catch(Exception exc)
                                    {
                                        if(this.debug)
                                        {
                                            System.out.println("[ERROR] problems updating guiMap:"+exc.toString());
                                        }
                                    }
                                }
                            }
                            
                            if(key.equals("PngImages"))
                            {
                                // Hashtable of random is keys to Bmp objects
                                type = islandSoftwareEngine.APP_PNG_GRAPHIC;
                                objIds = (Hashtable) iar.getGuiResourceData(key);
                                this._hasGraphics = true;

                                try
                                {
                                    imgHt = 0;
                                    imgWidth = 0;
                                    imgUrl = "";
                                    mediaName = "";

                                    for(Enumeration keyObjIds = objIds.keys(); keyObjIds.hasMoreElements();)
                                    {
                                        objId = (String) keyObjIds.nextElement();
                                        
                                        if(
                                            (!objId.equals("Name"))&&
                                            (!objId.equals("Width"))&&
                                            (!objId.equals("Height"))&&
                                            (!objId.equals("URL"))
                                                )
                                        {
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] adding image:"+objId);
                                            }
                                            
                                            // assume old school format
                                            mediaName = objId;
                                            imgUrl = objIds.get(objId);
                                                    
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] imgUrl="+imgUrl);
                                            }
                                                    
                                            if(imgUrl instanceof DataBuffer)
                                            {
                                                        if(imgUrl != "")
                                                        {
                                                            imgData = (DataBuffer) imgUrl;
                                                            img = imgData.getArray();
                                                            bmp = null;
                                                            
                                                            try
                                                            {
                                                                if(imgHt > 0 && imgWidth > 0)
                                                                {
                                                                    //bmp = new Bitmap(Bitmap.getDefaultType(),imgWidth,imgHt,img);
                                                                    //bmp = new Bitmap(Bitmap.ROWWISE_16BIT_COLOR,imgWidth,imgHt,img);
                                                                    EncodedImage ei = EncodedImage.createEncodedImage(img,0,img.length);
                                                                    //ei.scaleImage32(Fixed32.tenThouToFP(2000),Fixed32.tenThouToFP(2000));  // scales between 1000 and 9999 increase image size, over 10000 decreases (divide by 10000)
                                                                    bmp = ei.getBitmap();
                                                                }
                                                                else
                                                                {
                                                                    bmp = Bitmap.createBitmapFromBytes(img ,0,img.length,1);
                                                                }
                                                            }
                                                            catch(Exception e)
                                                            {
                                                                if(this.debug)
                                                                {
                                                                    System.out.println("[ERROR] problems creating png object:"+e.toString());
                                                                }
                                                            }
    
                                                            ggd = this.new GraphicGuiDetails();
                                                            ggd.Name = mediaName; // name[id]
                                                            ggd.GraphicsObject = bmp; // obj
                                                            
                                                            if(mediaName.equals("Icon")||mediaName.equals("Background"))
                                                            {
                                                                if(mediaName.equals("Icon"))
                                                                {
                                                                    ggd.Id = "0";
                                                                }
                                                                
                                                                if(mediaName.equals("Background"))
                                                                {
                                                                    ggd.Id = "1";
                                                                }
                                                            }
                                                            else
                                                            {
                                                                if(this.debug)
                                                                {
                                                                    System.out.println("[INFO] ! Background or Icon");
                                                                }
                                                                
                                                                ggd.Id = getGuiId(mediaName); // order number of bmp image 
                                                            }
                                                            
                                                            ggd.height = imgHt;
                                                            ggd.width = imgWidth;
                                                            obj = (Object) ggd;
                                                            //seGraphics.addElement(obj); // add to vector
    
                                                            if(seGraphics.size() == 0)
                                                            {
                                                                seGraphics.addElement(null); // blank Icon
                                                                seGraphics.addElement(null); // Blank BackgroundImage
                                                            }
    
                                                            if(this.debug)
                                                            {
                                                                System.out.println("[INFO] padded nulls");
                                                            }
                                                            
                                                            if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                            {
                                                                    // backfill with nulls
                                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                                    {
                                                                        seGraphics.addElement(null);
                    
                                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                                        {
                                                                            break;
                                                                        }
                                                                    }
                                                            }
                                                            
                                                            if(this.debug)
                                                            {
                                                                System.out.println("[INFO] padded seGraphics");
                                                            }
    
                                                            if(!seGraphics.contains(obj))
                                                            {
                                                                seGraphics.insertElementAt(obj,Integer.valueOf(ggd.Id).intValue());
                                                                this.guiMap.add(new Integer(type), obj); // add to type map
                                                            }
                                                            
                                                            if(this.debug)
                                                            {
                                                                System.out.println("[INFO] added object to seGraphics and guiMap");
                                                            }
                                                        }
                                            }
                                                    
                                            if(imgUrl instanceof Hashtable)
                                            {
                                                // get the attribute values for the image
                                                Hashtable imgAttributes = (Hashtable) imgUrl;
                                                        
                                                mediaName = (String)imgAttributes.get("Name");
                                                imgWidth = Integer.parseInt((String)imgAttributes.get("Width"));
                                                imgHt = Integer.parseInt((String) imgAttributes.get("Height"));
                                                imgUrl = (DataBuffer) imgAttributes.get(mediaName);
                                        
                                               
                                                // Get the key put it in ObjId and create the Details object
                                                if(imgUrl != "")
                                                {
                                                    
                                                    imgData = (DataBuffer) imgUrl;
                                                    img = imgData.getArray();
                                                    bmp = null;
            
                                                    try
                                                    {
                                                        if(imgHt > 0 && imgWidth > 0)
                                                        {
                                                            bmp = Bitmap.createBitmapFromBytes(img ,0,img.length,1);
                                                            //bmp = new Bitmap(Bitmap.getDefaultType(),imgWidth,imgHt,img);
                                                            //bmp = new Bitmap(Bitmap.ROWWISE_16BIT_COLOR,imgWidth,imgHt,img);
                                                        }
                                                        else
                                                        {
                                                            bmp = Bitmap.createBitmapFromBytes(img ,0,img.length,1);
                                                        }
                                                    
                                                    }
                                                    catch(Exception ex)
                                                    {
                                                        if(this.debug)
                                                        {
                                                            System.out.println("[ERROR] problems creating png object"+ex.toString());
                                                        }
                                                    }
                                                    
                                                    ggd = this.new GraphicGuiDetails();
                                                    ggd.Name = mediaName; // name[id]
                                                    ggd.GraphicsObject = bmp; // obj
                                                    //ggd.Id = String.valueOf(mediaCount); // order number of bmp image 
                                                    if(mediaName.equals("Icon")||mediaName.equals("Background"))
                                                    {
                                                        if(mediaName.equals("Icon"))
                                                        {
                                                            ggd.Id = "0";
                                                        }
                                                                        
                                                        if(mediaName.equals("Background"))
                                                        {
                                                            ggd.Id = "1";
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ggd.Id = getGuiId(mediaName); // order number of bmp image 
                                                    }
                                                    
                                                    ggd.height = imgHt;
                                                    ggd.width = imgWidth;
                                                    obj = (Object) ggd;
                                                    
                                                    if(seGraphics.size() == 0)
                                                    {
                                                        seGraphics.addElement(null); // blank Icon
                                                        seGraphics.addElement(null); // Blank BackgroundImage
                                                    }
                                                    
                                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                    {
                                                            // backfill with nulls
                                                            for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                            {
                                                                seGraphics.addElement(null);
            
                                                                if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                                {
                                                                    break;
                                                                }
                                                            }
                                                    }
            
                                                    if(!seGraphics.contains(obj))
                                                    {
                                                        seGraphics.insertElementAt(obj,Integer.valueOf(ggd.Id).intValue());
                                                        this.guiMap.add(new Integer(type), obj); // add to type map
                                                    }
                                                }
                                                
                                            }
                                        }
                                    }

                                    
                                }
                                catch(Exception exc)
                                {
                                    if(this.debug)
                                    {
                                        System.out.println("[ERROR] problems updating guiMap:"+exc.toString());
                                    }
                                }
                            }
                        }
                    }
                
                
                
                    // translation stuff
                    for(Enumeration keys = iar.getTranslationKeys(); keys.hasMoreElements();)
                    {
                        obj = (Object)keys.nextElement();
                                
                        if(obj instanceof Integer)
                        {
                            intKey = (Integer) obj;
                                
                            for(Enumeration enms = (Enumeration)iar.getTranslationData(intKey); enms.hasMoreElements();)
                            {
                                key = (String) enms.nextElement();
                                
                                if(this.debug)
                                {
                                    //System.out.println("[INFO] layout intKey is :->"+intKey);
                                    //System.out.println("[INFO] layout key is : ->"+key);
                                }
                                    
                                try
                                {
                                    // switch?
                                    switch(intKey.intValue())
                                    {
                                        case islandSoftwareEngine.APP_GRAPHIC_WIDTH:
                                            // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                            vIndx = this.guiGraphicExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                                dld = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    // set the Width
                                                    ggd.Width = key.substring(key.indexOf("_")+1);                                        
                                                    
                                                    // see if this is tied to a layout
                                                    dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    
                                                }
                                                
                                                if(dld != null)
                                                {
                                                    if(dld.Graphics!=null)
                                                    {
                                                        dld.SetGridLayoutGraphicWidth(ggd.Width, key);
                                                    }
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seGraphics.setElementAt(ggd,vIndx);
                                            }
                                            this._hasGraphics = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRAPHIC_WIDTH");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRAPHIC_HEIGHT:
                                            // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                            vIndx = this.guiGraphicExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                                dld = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(ggd == null)
                                                    {
                                                        ggd = this.new GraphicGuiDetails();
                                                        ggd.Name = this.getGuiGraphicName((String)key); // name[id]
                                                        ggd.Id = this.getGuiId((String)key);
                                                    }
                                                    
                                                    // set the Height
                                                    ggd.Height = key.substring(key.indexOf("_")+1);                                        
                                                    
                                                    // see if this is tied to a layout
                                                    dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    
                                                }
                                                
                                                if(dld != null)
                                                {
                                                    if(dld.Graphics !=null)
                                                    {
                                                        dld.SetGridLayoutGraphicHeight(ggd.Height, key);
                                                    }
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seGraphics.setElementAt(ggd,vIndx);
                                            }
                                            this._hasGraphics = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRAPHIC_HEIGHT");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRAPHIC_PLACEMENT:
                                            // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                            vIndx = this.guiGraphicExists((String)key);
                                            //key = (String)obj;
                                            GraphicGuiDetails g = null;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                                dld = null;
                                                
                                                if(this.debug)
                                                {
                                                    //System.out.println("[INFO] creating object for "+ggd.Name);
                                                    //System.out.println("[INFO] key was actually for "+key);
                                                }
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(ggd.Placement==null)
                                                    {
                                                        // set the x:y
                                                        ggd.Placement = key.substring(key.indexOf("_")+1);                                        
                                                    }
                                                    
                                                    if(!key.substring(0,key.indexOf("_")).equals(ggd.Name))
                                                    {
                                                        if(this.debug)
                                                        {
                                                            //System.out.println("[INFO] Since the names are different we change them");
                                                        }
                                                            g = new GraphicGuiDetails();
                                                            
                                                            g.Name = key.substring(0,key.indexOf("_"));
                                                            g.Placement = key.substring(key.indexOf("_")+1);                                        
                                                            g.PlacementSet = false;
                                                            g.GraphicsObject = ggd.GraphicsObject;
                                                            
                                                    }
                                                    
                                                    if(!ggd.Placement.equals(key.substring(key.indexOf("_")+1)))
                                                    {
                                                        if(g == null)
                                                        {
                                                            g = new GraphicGuiDetails();
                                                        }
                                                        
                                                        g.Placement = key.substring(key.indexOf("_")+1);                                        
                                                        g.PlacementSet = false;
                                                        g.GraphicsObject = ggd.GraphicsObject;
                                                    }
                                                    
                                                    // see if this is tied to a layout
                                                    dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    
                                                }
                                                
                                                if(dld != null)
                                                {
                                                    dld.hasGraphics = true;
                                                    
                                                    if(g == null)
                                                    {
                                                        dld.AddGraphicGuiDetails(ggd);
                                                    }
                                                    else
                                                    {
                                                        dld.AddGraphicGuiDetails(g);
                                                    }
                                                    
                                                    if(!dld.ScreenName.equals(""))
                                                    {
                                                        gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                        
                                                        if(g==null)
                                                        {
                                                            gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                        }
                                                        else
                                                        {
                                                            gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,g);
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if(!dld.Name.equals(""))
                                                        {
                                                            // set Screenname
                                                            dld.ScreenName = dld.Name.substring(0,dld.Name.indexOf("["));
                                                            
                                                            // update Layout vector
                                                            seLayouts.setElementAt(dld, dld.indx);
                                                            
                                                            // get Grid
                                                            gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                        
                                                            if(g == null)
                                                            {
                                                                // update the grid
                                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                            }
                                                            else
                                                            {
                                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,g);
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seGraphics.setElementAt(ggd,vIndx);
                                                
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiComponentName((String)key); // name[id]
                                                ggd.Placement = this.getGuiPlacement((String)key);
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                if(this.debug)
                                                {
                                                    //System.out.println("[INFO] creating object for "+ggd.Name);
                                                    //System.out.println("[INFO] key was actually for "+key);
                                                }
            
                                                // see if this is tied to a layout
                                                dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                
                                                if(dld != null)
                                                {
                                                    dld.hasGraphics = true;
                                                    dld.AddGraphicGuiDetails(ggd);
                                                    
                                                    if(!dld.ScreenName.equals(""))
                                                    {
                                                        gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                        gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                    }
                                                    else
                                                    {
                                                        if(!dld.Name.equals(""))
                                                        {
                                                            // set Screenname
                                                            dld.ScreenName = dld.Name.substring(0,dld.Name.indexOf("["));
                                                            
                                                            // update Layout vector
                                                            seLayouts.setElementAt(dld, dld.indx);
                                                            
                                                            // get Grid
                                                            gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                        
                                                            // update the grid
                                                            gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                    
                                                        }
                                                    }
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.parseInt(ggd.Id); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGraphics.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                        
                                            this._hasGraphics = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRAPHIC_PLACEMENT");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRAPHIC_PLACEMENTS:
                                            // find corresponding graphic and set  RandomImage[1]_x(+):y(+) 
                                            vIndx = this.guiGraphicExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(ggd.Placement == null)
                                                    {
                                                        if((ggd.xPlacementIncrement ==0)||(ggd.yPlacementIncrement == 0))
                                                        {
                                                            ggd.Placement = key.substring(key.indexOf("_")+1);
                                                            ggd.SetPlacementIncrement();
                                                        }
                                                    }                                        
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seGraphics.setElementAt(ggd,vIndx);
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiComponentName((String)key); // name[id]
                                                ggd.Placement = this.getGuiPlacements((String)key);
                                                ggd.SetPlacementIncrement();
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGraphics.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                        
                                            this._hasGraphics = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRAPHIC_PLACEMENTS");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRAPHIC_EVENT:
                                            vIndx = this.guiGraphicExists((String)key);
                                            //key = (String)key;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                                dld = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    // set the Event
                                                    ggd.Event = key.substring(key.indexOf("_")+1);                                        
                                                    ggd.AddEventToStack();
                                                
                                                    if(this.debug)
                                                    {
                                                        //System.out.println("[INFO] creating object for event:"+ggd.Event);
                                                    }
                                                    
                                                    // see if this is tied to a layout
                                                    dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    
                                                }
                                                
                                                if(dld != null)
                                                {
                                                    dld.SetGridLayoutGraphicEvent(ggd.Event, key);
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seGraphics.setElementAt(ggd,vIndx);
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiGraphicName((String)key); // name[id]
                                                ggd.Event = this.getGuiEvent((String)key);
                                                ggd.AddEventToStack();
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    
                                                if(dld != null)
                                                {
                                                    dld.SetGridLayoutGraphicEvent(ggd.Event, key);
                                                }
                                                
                                                if(this.debug)
                                                {
                                                    //System.out.println("[INFO] creating object for event:"+ggd.Event);
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGraphics.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                            this._hasGraphics = true;
                                                
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRAPHIC_EVENT");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRAPHIC_LOCATION:
                                            // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                            vIndx = this.guiGraphicExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    // set the Event
                                                    ggd.Location = key.substring(key.indexOf("_")+1);                                        
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seGraphics.setElementAt(ggd,vIndx);
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiGraphicName((String)key); // name[id]
                                                ggd.Location = this.getGuiLocation((String)key);
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGraphics.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                            this._hasGraphics = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRAPHIC_LOCATION");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRAPHIC_COLOR:
                                            // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                            vIndx = this.guiGraphicExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                                dld = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    // set the Color
                                                    ggd.SetColor(key.substring(key.indexOf("_")+1));                                        
                                                    
                                                    // see if this is tied to a layout
                                                    dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    
                                                }
                                                
                                                if(dld != null)
                                                {
                                                    dld.SetGridLayoutGraphicColor(ggd.ObjectColor, key);
                                                }
                                                
                                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seGraphics.addElement(null);
                                                        
                                                        if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seGraphics.setElementAt(ggd,vIndx);
                                            }
                                            
                                            this._hasGraphics = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRAPHIC_COLOR");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GUI_COLOR:
                                            // find corresponding graphic and set  TextBox[1]_Color:White 
                                            vIndx = this.guiComponentExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    // set the color
                                                    ggd.ObjectColor = getObjectColor(key);                                        
                                                }
                                                
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seComponents.setElementAt(ggd,vIndx);
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiComponentName((String)key); // name[id]
                                                ggd.ObjectColor = this.getObjectColor((String)key);
                                                ggd.ComponentsObject = this.getGuiComponent((String)key);
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                            this._hasComponents = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GUI_COLOR");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GUI_PLACEMENT:
                                            try
                                            {
                                                // find corresponding graphic and set  TextBox[1]_x:y 
                                                vIndx = this.guiComponentExists((String)key);
                                                //key = (String)obj;
                                                
                                                // if it already exists
                                                if(vIndx>=0)
                                                {
                                                    ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                                    
                                                    dld = null;
                                                    
                                                    // split value by _
                                                    if(key.indexOf("_")>=0)
                                                    {
                                                        if(ggd.Placement == null)
                                                        {
                                                            // set the x:y
                                                            ggd.Placement = key.substring(key.indexOf("_")+1);
                                                        }   
                                                                    
                                                        // see if this is tied to a layout
                                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    
                                                    }
                                                    
                                                    if(dld != null)
                                                    {
                                                        dld.hasComponents = true;
                                                        dld.AddGraphicGuiDetails(ggd);
                                                        
                                                        if(!dld.ScreenName.equals(""))
                                                        {
                                                            gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                            gsd.Layout = dld;
                                                            gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                        }
                                                        else
                                                        {
                                                            if(!dld.Name.equals(""))
                                                            {
                                                                // set Screenname
                                                                dld.ScreenName = dld.Name.substring(0,dld.Name.indexOf("["));
                                                                
                                                                // update Layout vector
                                                                seLayouts.setElementAt(dld, dld.indx);
                                                                
                                                                // get Grid
                                                                gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                            
                                                                gsd.Layout = dld;
                                                            
                                                                // update the grid
                                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                        
                                                            }
                                                        }
                                                    }
                                                    
                                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                    {
                                                        // backfill with nulls
                                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                        {
                                                            seComponents.addElement(null);
                                                            
                                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                            {
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    
                                                    // update the object
                                                    seComponents.setElementAt(ggd,vIndx);
                                                }
                                                else
                                                {
                                                    ggd = this.new GraphicGuiDetails();
                                                    ggd.Name = this.getGuiComponentName((String)key); // name[id]
                                                    ggd.Placement = this.getGuiPlacement((String)key);
                                                    ggd.ComponentsObject = this.getGuiComponent((String)key);
                                                    ggd.Id = this.getGuiId((String)key);
                                                    obj = (Object) ggd;
                                                    
                                                    // see if this is tied to a layout
                                                    dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    
                                                    if(dld != null)
                                                    {
                                                        dld.hasComponents = true;
                                                        dld.AddGraphicGuiDetails(ggd);
                                                        
                                                        if(!dld.ScreenName.equals(""))
                                                        {
                                                            gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                            gsd.Layout = dld;
                                                            gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                        }
                                                        else
                                                        {
                                                            if(!dld.Name.equals(""))
                                                            {
                                                                // set Screenname
                                                                dld.ScreenName = dld.Name.substring(0,dld.Name.indexOf("["));
                                                                
                                                                // update Layout vector
                                                                seLayouts.setElementAt(dld, dld.indx);
                                                                
                                                                // get Grid
                                                                gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                            
                                                                gsd.Layout = dld;
                                                            
                                                                // update the grid
                                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                        
                                                            }
                                                        }
                                                    }
                                                    
                                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                    {
                                                        // backfill with nulls
                                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                        {
                                                            seComponents.addElement(null);
                                                            
                                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                            {
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    
                                                    seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                                }
                                                this._hasComponents = true;
                                                if(this.debug)
                                                {
                                                    System.out.println("[INFO] element is of type APP_GUI_PLACEMENT");
                                                }
                                            }
                                            catch(Exception e)
                                            {
                                                System.out.println("[ERROR] problems loading objects for APP_GUI_PLACEMENT:"+e.toString());
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GUI_PLACEMENTS:
                                            // find corresponding graphic and set  TextBox[1]_x(+):y(+) 
                                            vIndx = this.guiComponentExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(ggd.Placement == null)
                                                    {
                                                        if((ggd.xPlacementIncrement == 0)||(ggd.yPlacementIncrement == 0))
                                                        {
                                                            // set the x:y
                                                            ggd.Placement = key.substring(key.indexOf("_")+1);
                                                            ggd.SetPlacementIncrement();
                                                        }
                                                    }                                       
                                                }
                                                
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seComponents.setElementAt(ggd,vIndx);
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiComponentName((String)key); // name[id]
                                                ggd.Placement = this.getGuiPlacements((String)key);
                                                ggd.SetPlacementIncrement();
                                                ggd.ComponentsObject = this.getGuiComponent((String)key);
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                            this._hasComponents = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GUI_PLACEMENTS");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GUI_EVENT:
                                            //
                                            vIndx = this.guiComponentExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    // set the EventName:Action
                                                    ggd.Event = key.substring(key.indexOf("_")+1);      
                                                    ggd.AddEventToStack();                                  
                                                
                                                    if(this.debug)
                                                    {
                                                        //System.out.println("[INFO] creating object for event:"+ggd.Event);
                                                    }
                                                }
                                                
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seComponents.setElementAt(ggd,vIndx);
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiComponentName((String)key); // name[id]
                                                ggd.Event = this.getGuiEvent((String)key);
                                                ggd.AddEventToStack();
                                                ggd.ComponentsObject = this.getGuiComponent((String)key);
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                
                                                if(this.debug)
                                                {
                                                    //System.out.println("[INFO] creating object for event:"+ggd.Event);
                                                }
            
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                            this._hasComponents = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GUI_EVENT");
                                            }
                                            break;
                                case islandSoftwareEngine.APP_GUI_HEIGHT:
                                            //
                                            vIndx = this.guiComponentExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                                dld = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(key.indexOf("Set")<0)
                                                    {
                                                        // set the height:value
                                                        if(key.substring(key.indexOf(":")+1).equals("DisplayHeight"))
                                                        {
                                                            ggd.height = this.displayHeight;  
                                                        }    
                                                        
                                                        if(key.substring(key.indexOf(":")+1).equals("LayoutHeight"))
                                                        {
                                                            ggd.Height = "LayoutHeight";  
                                                        }
                                                    }  
                                                    else
                                                    {
                                                        ggd.Height = key.substring(key.indexOf("_")+1);
                                                        
                                                        // see if this is tied to a layout
                                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    }
                                                }
                                                
                                                if(dld!=null)
                                                {
                                                    dld.SetGridLayoutComponentHeight(ggd.Height, key);
                                                }
                                                
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seComponents.setElementAt(ggd,vIndx);
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiComponentName((String)key); // name[id]
                                                dld = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(key.indexOf("Set")<0)
                                                    {
                                                        // set the height:value
                                                        if(key.substring(key.indexOf(":")+1).equals("DisplayHeight"))
                                                        {
                                                            ggd.height = this.displayHeight;  
                                                        }    
                                                        
                                                        if(key.substring(key.indexOf(":")+1).equals("LayoutHeight"))
                                                        {
                                                            ggd.Height = "LayoutHeight";  
                                                        }
                                                    }  
                                                    else
                                                    {
                                                        ggd.Height = key.substring(key.indexOf("_")+1);
                                                    
                                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    }
                                                }                                    
                                                
                                                ggd.ComponentsObject = this.getGuiComponent((String)key);
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                if(dld!=null)
                                                {
                                                    dld.SetGridLayoutComponentHeight(ggd.Height, key);
                                                }
            
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                            
                                            this._hasComponents = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GUI_HEIGHT");
                                            }
                                            break;
                                    
                                case islandSoftwareEngine.APP_GUI_WIDTH:
                                            //
                                            vIndx = this.guiComponentExists((String)key);
                                            //key = (String)obj;
                                            dld = null;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(key.indexOf("Set")<0)
                                                    {
                                                        // set the height:value
                                                        if(key.substring(key.indexOf(":")+1).equals("DisplayWidth"))
                                                        {
                                                            ggd.width = this.displayWidth;  
                                                        }    
                                                        
                                                        if(key.substring(key.indexOf(":")+1).equals("LayoutWidth"))
                                                        {
                                                            ggd.Width = "LayoutWidth";  
                                                        }
                                                    } 
                                                    else
                                                    {
                                                        ggd.Width = ggd.Width = key.substring(key.indexOf("_")+1);
                                                        
                                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    } 
                                                }   
                                                
                                                if(dld!=null)
                                                {
                                                    dld.SetGridLayoutComponentWidth(ggd.Width,key);
                                                }
                                                
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // update the object
                                                seComponents.setElementAt(ggd,vIndx);
                                            }
                                            else
                                            {
                                                ggd = this.new GraphicGuiDetails();
                                                ggd.Name = this.getGuiComponentName((String)key); // name[id]
                                                dld = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    
                                                    if(key.indexOf("Set")<0)
                                                    {
                                                        // set the height:value
                                                        if(key.substring(key.indexOf(":")+1).equals("DisplayWidth"))
                                                        {
                                                            ggd.width = this.displayWidth;  
                                                        }    
                                                        
                                                        if(key.substring(key.indexOf(":")+1).equals("LayoutWidth"))
                                                        {
                                                            ggd.Width = "LayoutWidth";  
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ggd.Width = ggd.Width = key.substring(key.indexOf("_")+1);
                                                        
                                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                                    }
                                                }                                    
                                                
                                                ggd.ComponentsObject = this.getGuiComponent((String)key);
                                                ggd.Id = this.getGuiId((String)key);
                                                obj = (Object) ggd;
                                                
                                                if(dld!=null)
                                                {
                                                    dld.SetGridLayoutComponentWidth(ggd.Width,key);
                                                }
                                                
                                                if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    // backfill with nulls
                                                    for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                                    {
                                                        seComponents.addElement(null);
                                                        
                                                        if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                            }
                                            this._hasComponents = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GUI_WIDTH");
                                            }
                                            break;
                                    }
                                }
                                catch(Exception exc)
                                {
                                    if(this.debug)
                                    {
                                        System.out.println("[ERROR] problems updating guiMap with translation file items:"+exc.toString());
                                    }
                                }
                            }
                        }
                    }
                }
            }
            catch(Exception ex)
            {
                if(this.debug)
                {
                    System.out.println("[ERROR] problems updating guiMap with resource file items:"+ex.toString());
                }
            }
           
        }
        else
        {
            try
            {
                // loop through these elements for dispersion
                for (Enumeration elementContainer = obs; elementContainer.hasMoreElements();) 
                {
                    obj = (Object) elementContainer.nextElement();
                    
                    
                        // switch?
                        switch(elementType)
                        {
                            case islandSoftwareEngine.APP_ICON_GRAPHIC:
                                
                                // create a bitmap from data
                                imgData = (DataBuffer) obj;
                                bmp = Bitmap.createBitmapFromBytes(imgData.getArray() ,0,imgData.getLength(),1);
                                obj = (Object) bmp;
                                ggd = this.new GraphicGuiDetails();
                                ggd.Name = "Icon_Image"; // name[id]
                                ggd.GraphicsObject = bmp; // obj
                                ggd.Id = "0"; // first and probably only Icon image
                                obj = (Object) ggd;
                                //seGraphics.addElement(obj); // add to vector

                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                }
                                
                                seGraphics.insertElementAt(obj,0);
                                
                                this._hasGraphics = true;
                                
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_ICON_GRAPHIC");
                                }
                                
                                // fill what details we know
                                
                                break;
                            case islandSoftwareEngine.APP_BACKGROUND_GRAPHIC:
                                
                                // create a bitmap from data
                                imgData = (DataBuffer) obj;
                                bmp = Bitmap.createBitmapFromBytes(imgData.getArray() ,0,imgData.getLength(),1);
                                obj = (Object) bmp;
                                ggd = this.new GraphicGuiDetails();
                                ggd.Name = "Background_Image"; // name[id]
                                ggd.GraphicsObject = bmp; // obj
                                ggd.Id = "1"; // first (and probably only) background image
                                obj = (Object) ggd;
                                
                                if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                
                                seGraphics.insertElementAt(obj,1); // add to vector
                                this._hasGraphics = true;
                                    
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_BACKGROUND_GRAPHIC");
                                }
                                
                                break;
                            case islandSoftwareEngine.APP_BMP_GRAPHIC:
                                // 
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_BMP_GRAPHIC");
                                }
                                break;
                             case islandSoftwareEngine.APP_PNG_GRAPHIC:
                                // 
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_PNG_GRAPHIC");
                                }
                                break;
                            case islandSoftwareEngine.APP_TEXT_GRAPHIC:
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_TEXT_GRAPHIC");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRAPHIC_WIDTH:
                                // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                vIndx = this.guiGraphicExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                    dld = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        if(ggd == null)
                                        {
                                            ggd = this.new GraphicGuiDetails();
                                            ggd.Name = this.getGuiGraphicName((String)key); // name[id]
                                            ggd.Id = this.getGuiId((String)key);
                                        }
                                                    
                                        // set the Width
                                        ggd.Width = key.substring(key.indexOf("_")+1);                                        
                                        
                                        // see if this is tied to a layout
                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        
                                    }
                                    
                                    if(dld != null)
                                    {
                                        if(dld.Graphics != null)
                                        {
                                            dld.SetGridLayoutGraphicWidth(ggd.Width, key);
                                        }
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seGraphics.setElementAt(ggd,vIndx);
                                }
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRAPHIC_WIDTH");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRAPHIC_HEIGHT:
                                // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                vIndx = this.guiGraphicExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                    dld = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        // set the Height
                                        ggd.Height = key.substring(key.indexOf("_")+1);                                        
                                        
                                        // see if this is tied to a layout
                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        
                                    }
                                    
                                    if(dld != null)
                                    {
                                        if(dld.Graphics!=null)
                                        {
                                            dld.SetGridLayoutGraphicHeight(ggd.Height, key);
                                        }
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seGraphics.setElementAt(ggd,vIndx);
                                }
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRAPHIC_HEIGHT");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRAPHIC_PLACEMENT:
                                // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                vIndx = this.guiGraphicExists((String)obj);
                                key = (String)obj;
                                GraphicGuiDetails g = null;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                    dld = null;
                                    
                                    if(this.debug)
                                    {
                                        //System.out.println("[INFO] creating object for "+ggd.Name);
                                        //System.out.println("[INFO] key was actually for "+key);
                                    }
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        if(ggd.Placement==null)
                                        {
                                            // set the x:y
                                            ggd.Placement = key.substring(key.indexOf("_")+1);                                        
                                        }
                                        
                                        if(!key.substring(0,key.indexOf("_")).equals(ggd.Name))
                                        {
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] Since the names are different we change them");
                                            }
                                                g = new GraphicGuiDetails();
                                                
                                                g.Name = key.substring(0,key.indexOf("_"));
                                                g.Placement = key.substring(key.indexOf("_")+1);                                        
                                                g.PlacementSet = false;
                                                g.GraphicsObject = ggd.GraphicsObject;
                                                
                                        }
                                        
                                        if(!ggd.Placement.equals(key.substring(key.indexOf("_")+1)))
                                        {
                                            if(g == null)
                                            {
                                                g = new GraphicGuiDetails();
                                            }
                                            
                                            g.Placement = key.substring(key.indexOf("_")+1);                                        
                                            g.PlacementSet = false;
                                            g.GraphicsObject = ggd.GraphicsObject;
                                        }
                                        
                                        // see if this is tied to a layout
                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        
                                    }
                                    
                                    if(dld != null)
                                    {
                                        dld.hasGraphics = true;
                                        
                                        if(g == null)
                                        {
                                            dld.AddGraphicGuiDetails(ggd);
                                        }
                                        else
                                        {
                                            dld.AddGraphicGuiDetails(g);
                                        }
                                        
                                        if(!dld.ScreenName.equals(""))
                                        {
                                            gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                            
                                            if(g==null)
                                            {
                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                            }
                                            else
                                            {
                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,g);
                                            }
                                        }
                                        else
                                        {
                                            if(!dld.Name.equals(""))
                                            {
                                                // set Screenname
                                                dld.ScreenName = dld.Name.substring(0,dld.Name.indexOf("["));
                                                
                                                // update Layout vector
                                                seLayouts.setElementAt(dld, dld.indx);
                                                
                                                // get Grid
                                                gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                            
                                                if(g == null)
                                                {
                                                    // update the grid
                                                    gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                                }
                                                else
                                                {
                                                    gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,g);
                                                }
                                            }
                                        }
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seGraphics.setElementAt(ggd,vIndx);
                                    
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiComponentName((String)obj); // name[id]
                                    ggd.Placement = this.getGuiPlacement((String)obj);
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    if(this.debug)
                                    {
                                        //System.out.println("[INFO] creating object for "+ggd.Name);
                                        //System.out.println("[INFO] key was actually for "+key);
                                    }

                                    // see if this is tied to a layout
                                    dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                    
                                    if(dld != null)
                                    {
                                        dld.hasGraphics = true;
                                        dld.AddGraphicGuiDetails(ggd);
                                        
                                        if(!dld.ScreenName.equals(""))
                                        {
                                            gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                            gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                        }
                                        else
                                        {
                                            if(!dld.Name.equals(""))
                                            {
                                                // set Screenname
                                                dld.ScreenName = dld.Name.substring(0,dld.Name.indexOf("["));
                                                
                                                // update Layout vector
                                                seLayouts.setElementAt(dld, dld.indx);
                                                
                                                // get Grid
                                                gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                            
                                                // update the grid
                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                        
                                            }
                                        }
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.parseInt(ggd.Id); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGraphics.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                            
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRAPHIC_PLACEMENT");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRAPHIC_PLACEMENTS:
                                // find corresponding graphic and set  RandomImage[1]_x(+):y(+) 
                                vIndx = this.guiGraphicExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        if(ggd.Placement == null)
                                        {
                                            if((ggd.xPlacementIncrement ==0)||(ggd.yPlacementIncrement == 0))
                                            {
                                                ggd.Placement = key.substring(key.indexOf("_")+1);
                                                ggd.SetPlacementIncrement();
                                            }
                                        }                                        
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seGraphics.setElementAt(ggd,vIndx);
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiComponentName((String)obj); // name[id]
                                    ggd.Placement = this.getGuiPlacements((String)obj);
                                    ggd.SetPlacementIncrement();
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGraphics.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                            
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRAPHIC_PLACEMENTS");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRAPHIC_EVENT:
                                vIndx = this.guiGraphicExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                    dld = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        // set the Event
                                        ggd.Event = key.substring(key.indexOf("_")+1);                                        
                                        ggd.AddEventToStack();
                                    
                                        if(this.debug)
                                        {
                                            //System.out.println("[INFO] creating object for event:"+ggd.Event);
                                        }
                                        
                                        // see if this is tied to a layout
                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        
                                    }
                                    
                                    if(dld != null)
                                    {
                                        dld.SetGridLayoutGraphicEvent(ggd.Event, key);
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seGraphics.setElementAt(ggd,vIndx);
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiGraphicName((String)obj); // name[id]
                                    ggd.Event = this.getGuiEvent((String)obj);
                                    ggd.AddEventToStack();
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        
                                    if(dld != null)
                                    {
                                        dld.SetGridLayoutGraphicEvent(ggd.Event, key);
                                    }
                                    
                                    if(this.debug)
                                    {
                                        //System.out.println("[INFO] creating object for event:"+ggd.Event);
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGraphics.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                                this._hasGraphics = true;
                                    
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRAPHIC_EVENT");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRAPHIC_LOCATION:
                                // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                vIndx = this.guiGraphicExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        // set the Event
                                        ggd.Location = key.substring(key.indexOf("_")+1);                                        
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seGraphics.setElementAt(ggd,vIndx);
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiGraphicName((String)obj); // name[id]
                                    ggd.Location = this.getGuiLocation((String)obj);
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGraphics.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRAPHIC_LOCATION");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRAPHIC_COLOR:
                                // find corresponding graphic and set  BackgroundImage[1]_x:y 
                                vIndx = this.guiGraphicExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seGraphics.elementAt(vIndx);
                                    dld = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        // set the Color
                                        ggd.SetColor(key.substring(key.indexOf("_")+1));                                        
                                        
                                        // see if this is tied to a layout
                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        
                                    }
                                    
                                    if(dld != null)
                                    {
                                        dld.SetGridLayoutGraphicColor(ggd.ObjectColor, key);
                                    }
                                    
                                    if(seGraphics.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seGraphics.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seGraphics.addElement(null);
                                            
                                            if(seGraphics.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seGraphics.setElementAt(ggd,vIndx);
                                }
                                
                                this._hasGraphics = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRAPHIC_COLOR");
                                }
                                break;
                            case islandSoftwareEngine.APP_GUI_COLOR:
                                // find corresponding graphic and set  TextBox[1]_Color:White 
                                vIndx = this.guiComponentExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        // set the color
                                        ggd.ObjectColor = getObjectColor(key);                                        
                                    }
                                    
                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seComponents.setElementAt(ggd,vIndx);
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiComponentName((String)obj); // name[id]
                                    ggd.ObjectColor = this.getObjectColor((String)obj);
                                    ggd.ComponentsObject = this.getGuiComponent((String)obj);
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                                this._hasComponents = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GUI_COLOR");
                                }
                                break;
                            case islandSoftwareEngine.APP_GUI_PLACEMENT:
                                try
                                {
                                    // find corresponding graphic and set  TextBox[1]_x:y 
                                    vIndx = this.guiComponentExists((String)obj);
                                    key = (String)obj;
                                    
                                    // if it already exists
                                    if(vIndx>=0)
                                    {
                                        ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                        
                                        dld = null;
                                        
                                        // split value by _
                                        if(key.indexOf("_")>=0)
                                        {
                                            if(ggd.Placement == null)
                                            {
                                                // set the x:y
                                                ggd.Placement = key.substring(key.indexOf("_")+1);
                                            }   
                                                        
                                            // see if this is tied to a layout
                                            dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        
                                        }
                                        
                                        if(dld != null)
                                        {
                                            dld.hasComponents = true;
                                            dld.AddGraphicGuiDetails(ggd);
                                            
                                            if(!dld.ScreenName.equals(""))
                                            {
                                                gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                gsd.Layout = dld;
                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                            }
                                            else
                                            {
                                                if(!dld.Name.equals(""))
                                                {
                                                    // set Screenname
                                                    dld.ScreenName = dld.Name.substring(0,dld.Name.indexOf("["));
                                                    
                                                    // update Layout vector
                                                    seLayouts.setElementAt(dld, dld.indx);
                                                    
                                                    // get Grid
                                                    gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                
                                                    gsd.Layout = dld;
                                                
                                                    // update the grid
                                                    gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                            
                                                }
                                            }
                                        }
                                        
                                        if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                        {
                                            // backfill with nulls
                                            for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                            {
                                                seComponents.addElement(null);
                                                
                                                if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    break;
                                                }
                                            }
                                        }
                                        
                                        // update the object
                                        seComponents.setElementAt(ggd,vIndx);
                                    }
                                    else
                                    {
                                        ggd = this.new GraphicGuiDetails();
                                        ggd.Name = this.getGuiComponentName((String)obj); // name[id]
                                        ggd.Placement = this.getGuiPlacement((String)obj);
                                        ggd.ComponentsObject = this.getGuiComponent((String)obj);
                                        ggd.Id = this.getGuiId((String)obj);
                                        obj = (Object) ggd;
                                        
                                        // see if this is tied to a layout
                                        dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        
                                        if(dld != null)
                                        {
                                            dld.hasComponents = true;
                                            dld.AddGraphicGuiDetails(ggd);
                                            
                                            if(!dld.ScreenName.equals(""))
                                            {
                                                gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                gsd.Layout = dld;
                                                gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                            }
                                            else
                                            {
                                                if(!dld.Name.equals(""))
                                                {
                                                    // set Screenname
                                                    dld.ScreenName = dld.Name.substring(0,dld.Name.indexOf("["));
                                                    
                                                    // update Layout vector
                                                    seLayouts.setElementAt(dld, dld.indx);
                                                    
                                                    // get Grid
                                                    gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(this.getGridId(dld.Name)));
                                                
                                                    gsd.Layout = dld;
                                                
                                                    // update the grid
                                                    gsd.SetScreenLayoutGgd(dld.ScreenName,dld.Name,ggd);
                                            
                                                }
                                            }
                                        }
                                        
                                        if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                        {
                                            // backfill with nulls
                                            for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                            {
                                                seComponents.addElement(null);
                                                
                                                if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                                {
                                                    break;
                                                }
                                            }
                                        }
                                        
                                        seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                    }
                                    this._hasComponents = true;
                                    if(this.debug)
                                    {
                                        System.out.println("[INFO] element is of type APP_GUI_PLACEMENT");
                                    }
                                }
                                catch(Exception e)
                                {
                                    System.out.println("[ERROR] problems loading objects for APP_GUI_PLACEMENT:"+e.toString());
                                }
                                break;
                            case islandSoftwareEngine.APP_GUI_PLACEMENTS:
                                // find corresponding graphic and set  TextBox[1]_x(+):y(+) 
                                vIndx = this.guiComponentExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        if(ggd.Placement == null)
                                        {
                                            if((ggd.xPlacementIncrement == 0)||(ggd.yPlacementIncrement == 0))
                                            {
                                                // set the x:y
                                                ggd.Placement = key.substring(key.indexOf("_")+1);
                                                ggd.SetPlacementIncrement();
                                            }
                                        }                                       
                                    }
                                    
                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seComponents.setElementAt(ggd,vIndx);
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiComponentName((String)obj); // name[id]
                                    ggd.Placement = this.getGuiPlacements((String)obj);
                                    ggd.SetPlacementIncrement();
                                    ggd.ComponentsObject = this.getGuiComponent((String)obj);
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                                this._hasComponents = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GUI_PLACEMENTS");
                                }
                                break;
                            case islandSoftwareEngine.APP_GUI_EVENT:
                                //
                                vIndx = this.guiComponentExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        // set the EventName:Action
                                        ggd.Event = key.substring(key.indexOf("_")+1);      
                                        ggd.AddEventToStack();                                  
                                    
                                        if(this.debug)
                                        {
                                            //System.out.println("[INFO] creating object for event:"+ggd.Event);
                                        }
                                    }
                                    
                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seComponents.setElementAt(ggd,vIndx);
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiComponentName((String)obj); // name[id]
                                    ggd.Event = this.getGuiEvent((String)obj);
                                    ggd.AddEventToStack();
                                    ggd.ComponentsObject = this.getGuiComponent((String)obj);
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    
                                    if(this.debug)
                                    {
                                        //System.out.println("[INFO] creating object for event:"+ggd.Event);
                                    }

                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                                this._hasComponents = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GUI_EVENT");
                                }
                                break;
                       case islandSoftwareEngine.APP_GUI_HEIGHT:
                                //
                                vIndx = this.guiComponentExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                    dld = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        if(key.indexOf("Set")<0)
                                        {
                                            // set the height:value
                                            if(key.substring(key.indexOf(":")+1).equals("DisplayHeight"))
                                            {
                                                ggd.height = this.displayHeight;  
                                            }    
                                            
                                            if(key.substring(key.indexOf(":")+1).equals("LayoutHeight"))
                                            {
                                                ggd.Height = "LayoutHeight";  
                                            }
                                        }  
                                        else
                                        {
                                            ggd.Height = key.substring(key.indexOf("_")+1);
                                            
                                            // see if this is tied to a layout
                                            dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        }
                                    }
                                    
                                    if(dld!=null)
                                    {
                                        dld.SetGridLayoutComponentHeight(ggd.Height, key);
                                    }
                                    
                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seComponents.setElementAt(ggd,vIndx);
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiComponentName((String)obj); // name[id]
                                    dld = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        if(key.indexOf("Set")<0)
                                        {
                                            // set the height:value
                                            if(key.substring(key.indexOf(":")+1).equals("DisplayHeight"))
                                            {
                                                ggd.height = this.displayHeight;  
                                            }    
                                            
                                            if(key.substring(key.indexOf(":")+1).equals("LayoutHeight"))
                                            {
                                                ggd.Height = "LayoutHeight";  
                                            }
                                        }  
                                        else
                                        {
                                            ggd.Height = key.substring(key.indexOf("_")+1);
                                        
                                            dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        }
                                    }                                    
                                    
                                    ggd.ComponentsObject = this.getGuiComponent((String)obj);
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    if(dld!=null)
                                    {
                                        dld.SetGridLayoutComponentHeight(ggd.Height, key);
                                    }

                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                                
                                this._hasComponents = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GUI_HEIGHT");
                                }
                                break;
                        
                       case islandSoftwareEngine.APP_GUI_WIDTH:
                                //
                                vIndx = this.guiComponentExists((String)obj);
                                key = (String)obj;
                                dld = null;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    ggd = (GraphicGuiDetails) seComponents.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        if(key.indexOf("Set")<0)
                                        {
                                            // set the height:value
                                            if(key.substring(key.indexOf(":")+1).equals("DisplayWidth"))
                                            {
                                                ggd.width = this.displayWidth;  
                                            }    
                                            
                                            if(key.substring(key.indexOf(":")+1).equals("LayoutWidth"))
                                            {
                                                ggd.Width = "LayoutWidth";  
                                            }
                                        } 
                                        else
                                        {
                                            ggd.Width = ggd.Width = key.substring(key.indexOf("_")+1);
                                            
                                            dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        } 
                                    }   
                                    
                                    if(dld!=null)
                                    {
                                        dld.SetGridLayoutComponentWidth(ggd.Width,key);
                                    }
                                    
                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // update the object
                                    seComponents.setElementAt(ggd,vIndx);
                                }
                                else
                                {
                                    ggd = this.new GraphicGuiDetails();
                                    ggd.Name = this.getGuiComponentName((String)obj); // name[id]
                                    dld = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        
                                        if(key.indexOf("Set")<0)
                                        {
                                            // set the height:value
                                            if(key.substring(key.indexOf(":")+1).equals("DisplayWidth"))
                                            {
                                                ggd.width = this.displayWidth;  
                                            }    
                                            
                                            if(key.substring(key.indexOf(":")+1).equals("LayoutWidth"))
                                            {
                                                ggd.Width = "LayoutWidth";  
                                            }
                                        }
                                        else
                                        {
                                            ggd.Width = ggd.Width = key.substring(key.indexOf("_")+1);
                                            
                                            dld = (DisplayLayoutDetails)seLayouts.elementAt(Integer.parseInt(this.getGuiLayoutId(key)));
                                        }
                                    }                                    
                                    
                                    ggd.ComponentsObject = this.getGuiComponent((String)obj);
                                    ggd.Id = this.getGuiId((String)obj);
                                    obj = (Object) ggd;
                                    
                                    if(dld!=null)
                                    {
                                        dld.SetGridLayoutComponentWidth(ggd.Width,key);
                                    }
                                    
                                    if(seComponents.size()<=Integer.valueOf(ggd.Id).intValue())
                                    {
                                        // backfill with nulls
                                        for(int a=seComponents.size(); a<Integer.valueOf(ggd.Id).intValue(); a++)
                                        {
                                            seComponents.addElement(null);
                                            
                                            if(seComponents.size()>Integer.valueOf(ggd.Id).intValue())
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seComponents.insertElementAt(ggd,Integer.valueOf(ggd.Id).intValue());
                                }
                                this._hasComponents = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GUI_WIDTH");
                                }
                                break;
                        }
                        
                    // save to gui map as graphic placement & event
                    this.guiMap.add(new Integer(elementType),obj);
                    
                }
            }
            catch(Exception e)
            {
                if(this.debug)
                {
                    System.out.println("[ERROR] problems updating guiMap with graphics and components:"+e.toString());
                    e.printStackTrace();
                }
            }
        }
    }
    
    public void SetLayoutMapElements(Enumeration obs, int elementType)
    {
        islandApplicationResource iar;
        DataBuffer imgData;
        Hashtable objIds;
        GraphicGuiDetails ggd;
        DisplayLayoutDetails dld;
        GridScreenDetails gsd;
        String objId;
        String key;
        Bitmap bmp; 
        Integer intKey;
        Object obj;
        byte[] img;
        int type;
        int vIndx;
            
        if(this.layoutMap == null)
        {
            this.layoutMap = new MultiMap();
        }   
        
        if(elementType == islandApplicationResource.FILE)
        {
            if(this.debug)
            {
                System.out.println("[INFO] handling application layout file");
            }
            
            // initializations
            obj = null;
            type = elementType;
             
            try
            {
                for(Enumeration element = obs; element.hasMoreElements();)
                {
                    obj = null;
            
                    // packed resources list
                    iar = (islandApplicationResource) element.nextElement();
                                
                    // add contents to guiMap
                    for(Enumeration keys = iar.getTranslationKeys(); keys.hasMoreElements();)
                    {
                        obj = (Object)keys.nextElement();
                            
                        if(obj instanceof String)
                        {
                            key = (String) obj;
                            
                            if(this.debug)
                            {
                                System.out.println("[INFO] layout key is :->"+key);
                            }
                        }
                        
                        if(obj instanceof Integer)
                        {
                            intKey = (Integer) obj;
                            
                            for(Enumeration enms = (Enumeration)iar.getTranslationData(intKey); enms.hasMoreElements();)
                            {
                                key = (String) enms.nextElement();
                            
                                if(this.debug)
                                {
                                    //System.out.println("[INFO] layout intKey is :->"+intKey);
                                    //System.out.println("[INFO] layout key is : ->"+key);
                                }
                                
                                try
                                {
                                    // switch?
                                    switch(intKey.intValue())
                                    {
                                        case islandSoftwareEngine.APP_GRID :
                                            
                                            vIndx = this.gridExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                //dld = (DisplayLayoutDetails) seLayouts.elementAt(vIndx);
                                                gsd  = (GridScreenDetails) seGrids.elementAt(vIndx);
                                                
                                                // Make sure it's got a value
                                                if(gsd.GridLayoutOS.equals(""))
                                                {
                                                    // set the origin os
                                                    gsd.GridLayoutOS = getGridLayoutOS(key); 
                                                }
                                                
                                                if(gsd.GridLayoutVersion.equals(""))
                                                {
                                                    // set the version of original display
                                                    gsd.GridLayoutVersion = getGridLayoutVersion(key);                                       
                                                }
                                                
                                                // update the object
                                                seGrids.setElementAt(gsd,vIndx);
                                            }
                                            else
                                            {
                                                gsd = this.new GridScreenDetails();
                                                gsd.GridName = this.getGridName((String)key); // name[id]
                                                gsd.GridIndex = Integer.parseInt(this.getGridId((String)key)); // grid [id]
                                                gsd.GridLayoutOS = this.getGridLayoutOS((String)key);
                                                gsd.GridLayoutVersion = this.getGridLayoutVersion((String)key);
                                                obj = (Object) gsd;
                                                
                                                // make room
                                                if(seGrids.size()<=gsd.GridIndex)
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                                    {
                                                        seGrids.addElement(null);
                                                        
                                                        if(seGrids.size()>gsd.GridIndex)
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGrids.insertElementAt(gsd,gsd.GridIndex);
                                            }
                                            
                                            this._hasLayouts = true;
                                            
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type GRID");
                                            }
                                            
                                            break;
                                        case islandSoftwareEngine.APP_GRID_COLOR :
                                            vIndx = this.gridExists((String)key);
                                            //key = (String)key;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                gsd = (GridScreenDetails) seGrids.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    // set the color
                                                    gsd.SetGridColor(key.substring(key.indexOf("_")+1));                                        
                                                }
                                                
                                                // update the object
                                                seGrids.setElementAt(gsd,vIndx);
                                            }
                                            else
                                            {
                                                gsd = this.new GridScreenDetails();
                                                gsd.ScreenName = this.getGridLayoutName((String)key); // name[id]
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    // set the color
                                                    gsd.SetGridColor(key.substring(key.indexOf("_")+1));                                        
                                                }
                                                
                                                // lookup the index
                                                gsd.GridIndex = Integer.parseInt(this.getGridId((String)key));
                                                
                                                obj = (Object) gsd;
                                                
                                                if(seGrids.size()<=gsd.GridIndex)
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                                    {
                                                        seGrids.addElement(null);
                                                        
                                                        if(seGrids.size()>gsd.GridIndex)
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGrids.insertElementAt(gsd, gsd.GridIndex);
                                            }
                                        
                                            this._hasLayouts = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRID_COLOR");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRID_EVENT :
                                            
                                            vIndx = this.gridExists((String)key);
                                            //key = (String)key;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                gsd = (GridScreenDetails) seGrids.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(key.indexOf("Init")>=0)
                                                    {
                                                        // set the event
                                                        gsd.Event = key.substring(key.indexOf("_")+1);
                                                        gsd.AddEventToStack();  
                                                        
                                                        // Layout Screen Name
                                                        gsd.ScreenName = key.substring(key.indexOf(":")+1);
                                                        gsd.AddScreenToGrid();                                      
                                                    }
                                                    else
                                                    {
                                                        // Add the event
                                                        gsd.AddEventToStack(key.substring(key.indexOf("_")+1));
                                                        
                                                        // Layout Screen Name
                                                        gsd.AddScreenToGrid(key.substring(key.indexOf(":")+1));
                                                    }
                                                    
                                                    
                                                    gsd.hasScreens = true;
                                                    gsd.hasLayouts = true;
                                                    
                                                    
                                                    // lookup the index
                                                    gsd.GridIndex = vIndx;
                                                }
                                                
                                                // update the object
                                                seGrids.setElementAt(gsd,vIndx);
                                            }
                                            else
                                            {
                                                gsd = this.new GridScreenDetails();
                                                
                                                if(key.indexOf("Init")>=0)
                                                {
                                                    // set the event
                                                    gsd.Event = key.substring(key.indexOf("_")+1);
                                                    gsd.AddEventToStack();  
                                                                                        
                                                    gsd.ScreenName = key.substring(key.indexOf(":")+1);
                                                    gsd.AddScreenToGrid();
                                                
                                                }
                                                else
                                                {
                                                    gsd.AddEventToStack(key.substring(key.indexOf("_")+1));
                                                    gsd.AddScreenToGrid(key.substring(key.indexOf(":")+1));
                                                }
                                                
                                                
                                                gsd.hasLayouts = true;
                                                gsd.hasScreens = true;
                                                
                                                // lookup the index
                                                gsd.GridIndex = Integer.parseInt(this.getGridId((String)key));
                                                
                                                obj = (Object) gsd;
                                                
                                                if(seGrids.size()<=gsd.GridIndex)
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                                    {
                                                        seGrids.addElement(null);
                                                        
                                                        if(seGrids.size()>gsd.GridIndex)
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGrids.insertElementAt(gsd, gsd.GridIndex);
                                            }
                                            
                                            this._hasLayouts = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRID_EVENT");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRID_LAYOUT :
                                            
                                            vIndx = this.gridLayoutExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                dld = (DisplayLayoutDetails) seLayouts.elementAt(vIndx);
                                                gsd = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    dld.Id = Integer.toString(vIndx);
                                                    dld.Name = getGridLayoutName(key);
                                                    
                                                    if(this.debug)
                                                    {
                                                        //System.out.println("[INFO]Creating object for Layout:"+dld.Name);
                                                    }
                                                    
                                                    if(key.indexOf(".")>=0)
                                                    {
                                                        dld.LayoutX = Double.parseDouble(key.substring(key.indexOf(":")+1));
                                                        dld.LayoutY = Double.parseDouble(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    }
                                                    else
                                                    {
                                                        dld.LayoutX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                                        dld.LayoutY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    }
                                                    
                                                    
                                                    // Get the grid screen associated with this layout
                                                    gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(getGridId(key)));
                                                }
                                                
                                                if(gsd != null)
                                                {
                                                    gsd.Layout = dld;
                                                    gsd.hasLayouts = true;
                                                    gsd.hasScreens = true;
                                                    gsd.ScreenName = key.substring(0,key.indexOf("["));
                                                    dld.GridMaxX = gsd.GridMaxX;
                                                    dld.GridMaxY = gsd.GridMaxY;
                                                    gsd.AddLayoutToScreen();
                                                    
                                                    if(this.debug)
                                                    {
                                                        System.out.println("[INFO] adding layout to Screen");
                                                    }
                                                    
                                                    gsd.AddScreenToGrid();
                                                    
                                                    if(this.debug)
                                                    {
                                                        System.out.println("[INFO] adding Screen To Grid");
                                                    }
                                                }
                                                
                                                // update the object
                                                seLayouts.setElementAt(dld,vIndx);
                                            }
                                            else
                                            {
                                                dld = this.new DisplayLayoutDetails();
                                                gsd = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    //dld.hasLayout = true;
                                                    dld.Id = getGridLayoutId(key);
                                                    dld.Name = getGridLayoutName(key);
                                                    
                                                    if(this.debug)
                                                    {
                                                        //System.out.println("[INFO]Creating object for Layout:"+dld.Name);
                                                    }
            
                                                    if(key.indexOf(".")>=0)
                                                    {
                                                        dld.LayoutX = Double.parseDouble(key.substring(key.indexOf(":")+1));
                                                        dld.LayoutY = Double.parseDouble(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    }
                                                    else
                                                    {
                                                        dld.LayoutX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                                        dld.LayoutY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    }
                                                    
                                                    // Get the grid screen associated with this layout
                                                    gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(getGridId(key)));
                                                }  
                                                
                                                obj = (Object) dld;
                                                
                                                if(seLayouts.size()<=Integer.parseInt(dld.Id))
                                                {
                                                    // backfill with nulls
                                                    for(int a=seLayouts.size(); a<Integer.parseInt(dld.Id); a++)
                                                    {
                                                        seLayouts.addElement(null);
                                                        
                                                        if(seLayouts.size()>Integer.parseInt(dld.Id))
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                
                                                if(gsd != null)
                                                {
                                                    gsd.Layout = dld;
                                                    gsd.hasLayouts = true;
                                                    gsd.hasScreens = true;
                                                    gsd.ScreenName = key.substring(0,key.indexOf("["));
                                                    dld.GridMaxX = gsd.GridMaxX;
                                                    dld.GridMaxY = gsd.GridMaxY;
                                                    gsd.AddLayoutToScreen();
                                                    
                                                    if(this.debug)
                                                    {
                                                        System.out.println("[INFO] Adding Layout to Screen");
                                                    }
                                                    
                                                    gsd.AddScreenToGrid();
                                                    
                                                    if(this.debug)
                                                    {
                                                        System.out.println("[INFO] Adding Screen To Grid");
                                                    }
                                                }
                                            
                                            
                                                //if(!seLayouts.contains(dld))
                                                {
                                                    seLayouts.insertElementAt(dld, Integer.parseInt(dld.Id));
                                                }
                                            }
                                        
                                            this._hasLayouts = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRID_LAYOUT");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRID_LAYOUT_COLOR :
                                            vIndx = this.gridLayoutExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                dld = (DisplayLayoutDetails) seLayouts.elementAt(vIndx);
                                                gsd = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_Color")>=0)
                                                {
                                                    if(dld == null)
                                                    {
                                                        dld = this.new DisplayLayoutDetails();
                                                    }
                                                    
                                                    //dld.hasLayout = true;
                                                    dld.Id = Integer.toString(vIndx);
                                                    dld.Name = getGridLayoutName(key);
                                                    //dld.GridIndex = Integer.parseInt(getGridId(key));
                                                    //dld.GridName = getGridName(key); 
                                                    if(key.indexOf(".")>=0)
                                                    {
                                                        dld.SetLayoutColor(key.substring(key.indexOf(":")+1));
                                                    }
                                                    else
                                                    {
                                                        dld.SetLayoutColor(key.substring(key.indexOf(":")+1));
                                                    }
                                                    
                                                    
                                                    // Get the grid screen associated with this layout
                                                    gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(getGridId(key)));
                                                }
                                                
                                                if(gsd != null)
                                                {
                                                    gsd.Layout = dld;
                                                    gsd.hasLayouts = true;
                                                    gsd.hasScreens = true;
                                                    gsd.ScreenName = key.substring(0,key.indexOf("["));
                                                    dld.GridMaxX = gsd.GridMaxX;
                                                    dld.GridMaxY = gsd.GridMaxY;
                                                    gsd.AddLayoutToScreen();
                                                    gsd.AddScreenToGrid();
                                                    
                                                }
                                                
                                                // update the object
                                                seLayouts.setElementAt(dld,vIndx);
                                            }
                                            
                                            this._hasLayouts = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRID_LAYOUT_COLOR");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRID_LAYOUT_ORIGIN :
                                            vIndx = this.gridLayoutExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                dld = (DisplayLayoutDetails) seLayouts.elementAt(vIndx);
                                                gsd = null;
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    if(dld == null)
                                                    {
                                                        dld = this.new DisplayLayoutDetails();
                                                    }
                                                    
                                                    dld.Id = Integer.toString(vIndx);
                                                    dld.Name = getGridLayoutName(key);
                                                    
                                                    if(key.indexOf(".")>=0)
                                                    {
                                                        //dld.LayoutPlacementX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                                        //dld.LayoutPlacementY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    
                                                        dld.LayoutPlacementX = Double.parseDouble(key.substring(key.indexOf(":")+1));
                                                        dld.LayoutPlacementY = Double.parseDouble(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    }
                                                    else
                                                    {
                                                        //dld.LayoutPlacementX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                                        //dld.LayoutPlacementY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    
                                                        dld.LayoutPlacementX = Double.parseDouble(key.substring(key.indexOf(":")+1));
                                                        dld.LayoutPlacementY = Double.parseDouble(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    }
                                                    
                                                    
                                                    // Get the grid screen associated with this layout
                                                    gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(getGridId(key)));
                                                }
                                                
                                                if(gsd != null)
                                                {
                                                    gsd.Layout = dld;
                                                    gsd.hasLayouts = true;
                                                    gsd.hasScreens = true;
                                                    gsd.ScreenName = key.substring(0,key.indexOf("["));
                                                    dld.GridMaxX = gsd.GridMaxX;
                                                    dld.GridMaxY = gsd.GridMaxY;
                                                    gsd.AddLayoutToScreen();
                                                    gsd.AddScreenToGrid();
                                                    
                                                }
                                                
                                                // update the object
                                                seLayouts.setElementAt(dld,vIndx);
                                            }
                                            
                                            this._hasLayouts = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRID_LAYOUT_ORIGIN");
                                            }
                                            break;
                                        case islandSoftwareEngine.APP_GRID_MAX :
                                            
                                            vIndx = this.gridExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                gsd = (GridScreenDetails) seGrids.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    gsd.GridMaxX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                                    gsd.GridMaxY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                }
                                                
                                                // update the object
                                                seGrids.setElementAt(gsd,vIndx);
                                            }
                                            else
                                            {
                                                gsd = this.new GridScreenDetails();
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    gsd.GridMaxX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                                    gsd.GridMaxY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                }
                                                
                                                gsd.GridName = key.substring(0,key.indexOf("_"));
                                                    
                                                // lookup the index
                                                gsd.GridIndex = Integer.parseInt(this.getGridId((String)key));
                                                
                                                obj = (Object) gsd;
                                                
                                                if(seGrids.size()<=gsd.GridIndex)
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                                    {
                                                        seGrids.addElement(null);
                                                        
                                                        if(seGrids.size()>gsd.GridIndex)
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGrids.insertElementAt(gsd, gsd.GridIndex);
                                            }
                                            this._hasLayouts = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRID_MAX");
                                            }
                                            
                                            break;
                                        case islandSoftwareEngine.APP_GRID_ORIGIN :
                                            
                                            vIndx = this.gridExists((String)key);
                                            //key = (String)obj;
                                            
                                            // if it already exists
                                            if(vIndx>=0)
                                            {
                                                gsd = (GridScreenDetails) seGrids.elementAt(vIndx);
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    gsd.GridOriginX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                                    gsd.GridOriginY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    gsd.Origin = key.substring(key.indexOf("_")+1);
                                                }
                                                
                                                // update the object
                                                seGrids.setElementAt(gsd,vIndx);
                                            }
                                            else
                                            {
                                                gsd = this.new GridScreenDetails();
                                                
                                                // split value by _
                                                if(key.indexOf("_")>=0)
                                                {
                                                    gsd.GridOriginX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                                    gsd.GridOriginY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    gsd.Origin = key.substring(key.indexOf("_")+1);
                                                }
                                                
                                                gsd.GridName = key.substring(0,key.indexOf("_"));
                                                    
                                                // lookup the index
                                                gsd.GridIndex = Integer.parseInt(this.getGridId((String)key));
                                                
                                                obj = (Object) gsd;
                                                
                                                if(seGrids.size()<=gsd.GridIndex)
                                                {
                                                    // backfill with nulls
                                                    for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                                    {
                                                        seGrids.addElement(null);
                                                        
                                                        if(seGrids.size()>gsd.GridIndex)
                                                        {
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                seGrids.insertElementAt(gsd, gsd.GridIndex);
                                            }
                                            
                                            this._hasLayouts = true;
                                            if(this.debug)
                                            {
                                                System.out.println("[INFO] element is of type APP_GRID_ORIGIN");
                                            }
                                            break; 
                                    }
                                }
                                catch(Exception exc)
                                {
                                    if(this.debug)
                                    {
                                        System.out.println("[ERROR] problems setting layout map element from the download file"+exc.toString());
                                    }
                                }
                            }
                        }
                    }
                }
            }
            catch(Exception ex)
            {
                if(this.debug)
                {
                    System.out.println("[ERROR] problems setting layout map elements:"+ex.toString());
                }
            }
            
        }
        else
        {
            try
            {
                 // loop through these elements for dispersion
                for (Enumeration elementContainer = obs; elementContainer.hasMoreElements();) 
                {
                    obj = (Object) elementContainer.nextElement();
                    
                    
                        // switch?
                        switch(elementType)
                        {
                            case islandSoftwareEngine.APP_GRID :
                                
                                vIndx = this.gridExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    //dld = (DisplayLayoutDetails) seLayouts.elementAt(vIndx);
                                    gsd  = (GridScreenDetails) seGrids.elementAt(vIndx);
                                    
                                    // Make sure it's got a value
                                    if(gsd.GridLayoutOS.equals(""))
                                    {
                                        // set the origin os
                                        gsd.GridLayoutOS = getGridLayoutOS(key); 
                                    }
                                    
                                    if(gsd.GridLayoutVersion.equals(""))
                                    {
                                        // set the version of original display
                                        gsd.GridLayoutVersion = getGridLayoutVersion(key);                                       
                                    }
                                    
                                    // update the object
                                    seGrids.setElementAt(gsd,vIndx);
                                }
                                else
                                {
                                    gsd = this.new GridScreenDetails();
                                    gsd.GridName = this.getGridName((String)obj); // name[id]
                                    gsd.GridIndex = Integer.parseInt(this.getGridId((String)obj)); // grid [id]
                                    gsd.GridLayoutOS = this.getGridLayoutOS((String)obj);
                                    gsd.GridLayoutVersion = this.getGridLayoutVersion((String)obj);
                                    obj = (Object) gsd;
                                    
                                    // make room
                                    if(seGrids.size()<=gsd.GridIndex)
                                    {
                                        // backfill with nulls
                                        for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                        {
                                            seGrids.addElement(null);
                                            
                                            if(seGrids.size()>gsd.GridIndex)
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGrids.insertElementAt(gsd,gsd.GridIndex);
                                }
                                
                                this._hasLayouts = true;
                                
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type GRID");
                                }
                                
                                break;
                            case islandSoftwareEngine.APP_GRID_COLOR :
                                vIndx = this.gridExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    gsd = (GridScreenDetails) seGrids.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        // set the color
                                        gsd.SetGridColor(key.substring(key.indexOf("_")+1));                                        
                                    }
                                    
                                    // update the object
                                    seGrids.setElementAt(gsd,vIndx);
                                }
                                else
                                {
                                    gsd = this.new GridScreenDetails();
                                    gsd.ScreenName = this.getGridLayoutName((String)obj); // name[id]
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        // set the color
                                        gsd.SetGridColor(key.substring(key.indexOf("_")+1));                                        
                                    }
                                    
                                    // lookup the index
                                    gsd.GridIndex = Integer.parseInt(this.getGridId((String)obj));
                                    
                                    obj = (Object) gsd;
                                    
                                    if(seGrids.size()<=gsd.GridIndex)
                                    {
                                        // backfill with nulls
                                        for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                        {
                                            seGrids.addElement(null);
                                            
                                            if(seGrids.size()>gsd.GridIndex)
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGrids.insertElementAt(gsd, gsd.GridIndex);
                                }
                            
                                this._hasLayouts = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRID_COLOR");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRID_EVENT :
                                
                                vIndx = this.gridExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    gsd = (GridScreenDetails) seGrids.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        if(key.indexOf("Init")>=0)
                                        {
                                            // set the event
                                            gsd.Event = key.substring(key.indexOf("_")+1);
                                            gsd.AddEventToStack();  
                                            
                                            // Layout Screen Name
                                            gsd.ScreenName = key.substring(key.indexOf(":")+1);
                                            gsd.AddScreenToGrid();                                      
                                        }
                                        else
                                        {
                                            // Add the event
                                            gsd.AddEventToStack(key.substring(key.indexOf("_")+1));
                                            
                                            // Layout Screen Name
                                            gsd.AddScreenToGrid(key.substring(key.indexOf(":")+1));
                                        }
                                        
                                        
                                        gsd.hasScreens = true;
                                        gsd.hasLayouts = true;
                                        
                                        
                                        // lookup the index
                                        gsd.GridIndex = vIndx;
                                    }
                                    
                                    // update the object
                                    seGrids.setElementAt(gsd,vIndx);
                                }
                                else
                                {
                                    gsd = this.new GridScreenDetails();
                                    
                                    if(key.indexOf("Init")>=0)
                                    {
                                        // set the event
                                        gsd.Event = key.substring(key.indexOf("_")+1);
                                        gsd.AddEventToStack();  
                                                                              
                                        gsd.ScreenName = key.substring(key.indexOf(":")+1);
                                        gsd.AddScreenToGrid();
                                    
                                    }
                                    else
                                    {
                                        gsd.AddEventToStack(key.substring(key.indexOf("_")+1));
                                        gsd.AddScreenToGrid(key.substring(key.indexOf(":")+1));
                                    }
                                    
                                    
                                    gsd.hasLayouts = true;
                                    gsd.hasScreens = true;
                                    
                                    // lookup the index
                                    gsd.GridIndex = Integer.parseInt(this.getGridId((String)obj));
                                    
                                    obj = (Object) gsd;
                                    
                                    if(seGrids.size()<=gsd.GridIndex)
                                    {
                                        // backfill with nulls
                                        for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                        {
                                            seGrids.addElement(null);
                                            
                                            if(seGrids.size()>gsd.GridIndex)
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGrids.insertElementAt(gsd, gsd.GridIndex);
                                }
                                
                                this._hasLayouts = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRID_EVENT");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRID_LAYOUT :
                                
                                vIndx = this.gridLayoutExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    dld = (DisplayLayoutDetails) seLayouts.elementAt(vIndx);
                                    gsd = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        dld.Id = Integer.toString(vIndx);
                                        dld.Name = getGridLayoutName(key);
                                        
                                        if(this.debug)
                                        {
                                            //System.out.println("[INFO]Creating object for Layout:"+dld.Name);
                                        }
                                        
                                        if(key.indexOf(".")>=0)
                                        {
                                            dld.LayoutX = Double.parseDouble(key.substring(key.indexOf(":")+1));
                                            dld.LayoutY = Double.parseDouble(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                        }
                                        else
                                        {
                                            dld.LayoutX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                            dld.LayoutY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                        }
                                        
                                        
                                        // Get the grid screen associated with this layout
                                        gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(getGridId(key)));
                                    }
                                    
                                    if(gsd != null)
                                    {
                                        gsd.Layout = dld;
                                        gsd.hasLayouts = true;
                                        gsd.hasScreens = true;
                                        gsd.ScreenName = key.substring(0,key.indexOf("["));
                                        dld.GridMaxX = gsd.GridMaxX;
                                        dld.GridMaxY = gsd.GridMaxY;
                                        gsd.AddLayoutToScreen();
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] adding layout to Screen");
                                        }
                                        
                                        gsd.AddScreenToGrid();
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] adding Screen To Grid");
                                        }
                                    }
                                    
                                    // update the object
                                    seLayouts.setElementAt(dld,vIndx);
                                }
                                else
                                {
                                    dld = this.new DisplayLayoutDetails();
                                    gsd = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        //dld.hasLayout = true;
                                        dld.Id = getGridLayoutId(key);
                                        dld.Name = getGridLayoutName(key);
                                        
                                        if(this.debug)
                                        {
                                            //System.out.println("[INFO]Creating object for Layout:"+dld.Name);
                                        }

                                        if(key.indexOf(".")>=0)
                                        {
                                            dld.LayoutX = Double.parseDouble(key.substring(key.indexOf(":")+1));
                                            dld.LayoutY = Double.parseDouble(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                        }
                                        else
                                        {
                                            dld.LayoutX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                            dld.LayoutY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                        }
                                        
                                        // Get the grid screen associated with this layout
                                        gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(getGridId(key)));
                                    }  
                                    
                                    obj = (Object) dld;
                                    
                                    if(seLayouts.size()<=Integer.parseInt(dld.Id))
                                    {
                                        // backfill with nulls
                                        for(int a=seLayouts.size(); a<Integer.parseInt(dld.Id); a++)
                                        {
                                            seLayouts.addElement(null);
                                            
                                            if(seLayouts.size()>Integer.parseInt(dld.Id))
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    
                                    if(gsd != null)
                                    {
                                        gsd.Layout = dld;
                                        gsd.hasLayouts = true;
                                        gsd.hasScreens = true;
                                        gsd.ScreenName = key.substring(0,key.indexOf("["));
                                        dld.GridMaxX = gsd.GridMaxX;
                                        dld.GridMaxY = gsd.GridMaxY;
                                        gsd.AddLayoutToScreen();
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] Adding Layout to Screen");
                                        }
                                        
                                        gsd.AddScreenToGrid();
                                        
                                        if(this.debug)
                                        {
                                            System.out.println("[INFO] Adding Screen To Grid");
                                        }
                                    }
                                 
                                 
                                    //if(!seLayouts.contains(dld))
                                    {
                                        seLayouts.insertElementAt(dld, Integer.parseInt(dld.Id));
                                    }
                                }
                            
                                this._hasLayouts = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRID_LAYOUT");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRID_LAYOUT_COLOR :
                                vIndx = this.gridLayoutExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    dld = (DisplayLayoutDetails) seLayouts.elementAt(vIndx);
                                    gsd = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_Color")>=0)
                                    {
                                        //dld.hasLayout = true;
                                        dld.Id = Integer.toString(vIndx);
                                        dld.Name = getGridLayoutName(key);
                                        //dld.GridIndex = Integer.parseInt(getGridId(key));
                                        //dld.GridName = getGridName(key); 
                                        if(key.indexOf(".")>=0)
                                        {
                                            dld.SetLayoutColor(key.substring(key.indexOf(":")+1));
                                        }
                                        else
                                        {
                                            dld.SetLayoutColor(key.substring(key.indexOf(":")+1));
                                        }
                                        
                                        
                                        // Get the grid screen associated with this layout
                                        gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(getGridId(key)));
                                    }
                                    
                                    if(gsd != null)
                                    {
                                        gsd.Layout = dld;
                                        gsd.hasLayouts = true;
                                        gsd.hasScreens = true;
                                        gsd.ScreenName = key.substring(0,key.indexOf("["));
                                        dld.GridMaxX = gsd.GridMaxX;
                                        dld.GridMaxY = gsd.GridMaxY;
                                        gsd.AddLayoutToScreen();
                                        gsd.AddScreenToGrid();
                                        
                                    }
                                    
                                    // update the object
                                    seLayouts.setElementAt(dld,vIndx);
                                }
                                
                                this._hasLayouts = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRID_LAYOUT_COLOR");
                                }
                                break;
                            case islandSoftwareEngine.APP_GRID_LAYOUT_ORIGIN :
                                vIndx = this.gridLayoutExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    dld = (DisplayLayoutDetails) seLayouts.elementAt(vIndx);
                                    gsd = null;
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        dld.Id = Integer.toString(vIndx);
                                        dld.Name = getGridLayoutName(key);
                                        
                                        if(key.indexOf(".")>=0)
                                        {
                                            //dld.LayoutPlacementX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                            //dld.LayoutPlacementY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                            
                                            dld.LayoutPlacementX = Double.parseDouble(key.substring(key.indexOf(":")+1));
                                            dld.LayoutPlacementY = Double.parseDouble(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    
                                        }
                                        else
                                        {
                                            //dld.LayoutPlacementX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                            //dld.LayoutPlacementY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                        
                                            dld.LayoutPlacementX = Double.parseDouble(key.substring(key.indexOf(":")+1));
                                            dld.LayoutPlacementY = Double.parseDouble(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                                    
                                        }
                                        
                                        
                                        // Get the grid screen associated with this layout
                                        gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(getGridId(key)));
                                    }
                                    
                                    if(gsd != null)
                                    {
                                        gsd.Layout = dld;
                                        gsd.hasLayouts = true;
                                        gsd.hasScreens = true;
                                        gsd.ScreenName = key.substring(0,key.indexOf("["));
                                        dld.GridMaxX = gsd.GridMaxX;
                                        dld.GridMaxY = gsd.GridMaxY;
                                        gsd.AddLayoutToScreen();
                                        gsd.AddScreenToGrid();
                                        
                                    }
                                    
                                    // update the object
                                    seLayouts.setElementAt(dld,vIndx);
                                }
                                
                                this._hasLayouts = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRID_LAYOUT_ORIGIN");
                                }break;
                            case islandSoftwareEngine.APP_GRID_MAX :
                                
                                vIndx = this.gridExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    gsd = (GridScreenDetails) seGrids.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        gsd.GridMaxX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                        gsd.GridMaxY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                    }
                                    
                                    // update the object
                                    seGrids.setElementAt(gsd,vIndx);
                                }
                                else
                                {
                                    gsd = this.new GridScreenDetails();
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        gsd.GridMaxX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                        gsd.GridMaxY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                    }
                                    
                                    gsd.GridName = key.substring(0,key.indexOf("_"));
                                        
                                    // lookup the index
                                    gsd.GridIndex = Integer.parseInt(this.getGridId((String)obj));
                                    
                                    obj = (Object) gsd;
                                    
                                    if(seGrids.size()<=gsd.GridIndex)
                                    {
                                        // backfill with nulls
                                        for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                        {
                                            seGrids.addElement(null);
                                            
                                            if(seGrids.size()>gsd.GridIndex)
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGrids.insertElementAt(gsd, gsd.GridIndex);
                                }
                                this._hasLayouts = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRID_MAX");
                                }
                                
                                break;
                            case islandSoftwareEngine.APP_GRID_ORIGIN :
                                
                                vIndx = this.gridExists((String)obj);
                                key = (String)obj;
                                
                                // if it already exists
                                if(vIndx>=0)
                                {
                                    gsd = (GridScreenDetails) seGrids.elementAt(vIndx);
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        gsd.GridOriginX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                        gsd.GridOriginY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                        gsd.Origin = key.substring(key.indexOf("_")+1);
                                    }
                                    
                                    // update the object
                                    seGrids.setElementAt(gsd,vIndx);
                                }
                                else
                                {
                                    gsd = this.new GridScreenDetails();
                                    
                                    // split value by _
                                    if(key.indexOf("_")>=0)
                                    {
                                        gsd.GridOriginX = Integer.parseInt(key.substring(key.indexOf(":")+1));
                                        gsd.GridOriginY = Integer.parseInt(key.substring(key.indexOf("_")+1,key.indexOf(":")));
                                        gsd.Origin = key.substring(key.indexOf("_")+1);
                                    }
                                    
                                    gsd.GridName = key.substring(0,key.indexOf("_"));
                                        
                                    // lookup the index
                                    gsd.GridIndex = Integer.parseInt(this.getGridId((String)obj));
                                    
                                    obj = (Object) gsd;
                                    
                                    if(seGrids.size()<=gsd.GridIndex)
                                    {
                                        // backfill with nulls
                                        for(int a=seGrids.size(); a<gsd.GridIndex; a++)
                                        {
                                            seGrids.addElement(null);
                                            
                                            if(seGrids.size()>gsd.GridIndex)
                                            {
                                                break;
                                            }
                                        }
                                    }
                                    
                                    seGrids.insertElementAt(gsd, gsd.GridIndex);
                                }
                                
                                this._hasLayouts = true;
                                if(this.debug)
                                {
                                    System.out.println("[INFO] element is of type APP_GRID_ORIGIN");
                                }
                                break; 
                        }
                }
            }
            catch(Exception e)
            {
                if(this.debug)
                {
                    System.out.println("[ERROR] problems updating layoutMap with layouts, graphics and components:"+e.toString());
                    e.printStackTrace();
                }
            }
        }
    }
    
    public int GetEngineStatus()
    {
        return currentStatus;
    }
    
    public int GetVKernelStatus()
    {
        return currentKernelStatus;
    }
    
    public int GetEngineState()
    {
        return currentState;
    }
    
    public boolean CompleteUserAction()
    {
        boolean actionCompleted= false;
        
        return actionCompleted;
    }
    
    public boolean NotifyUser()
    {
        boolean notificationSet = false;
        
        return notificationSet;
    }
    
    public boolean PerformOperationForEvent(String event, String value, String content)
    {
        GraphicGuiDetails ggd = null;
        ContentManagementDetails cmd = null;
        boolean operationPerformed = false;
        Hashtable componentChanges = null;
        Hashtable graphicChanges = null;
        Hashtable contentChanges = null;
        String ev = "";
        String regex = "";
        String regexStart = "";
        String regexEnd = "";
        String changeKey = "";
        String changeValue = "";
        int matchCount = 0;
        int matchFrom = 0;
        int matchTo = 0;
        int objIndx = 0;
        
        try
        {
            if(event.equals("Loaded"))
            {
                componentChanges = new Hashtable();
                graphicChanges = new Hashtable();
               
                // hide current components
                for(Enumeration com_e = seComponents.elements(); com_e.hasMoreElements();)
                {
                    ggd = (GraphicGuiDetails) com_e.nextElement();
                    
                    if(ggd !=null)
                    {
                        ggd.Event = "Removal";
                    }
                }

                // hide current graphics
                for(Enumeration gr_e = seGraphics.elements(); gr_e.hasMoreElements();)
                {
                    ggd = (GraphicGuiDetails) gr_e.nextElement();
                    
                    if(ggd !=null)
                    {
                        ggd.Event = "Removal";
                    }
                }
                
                // loop through content vector
                for(Enumeration ce = seContent.elements(); ce.hasMoreElements();)
                {
                    cmd = (ContentManagementDetails) ce.nextElement();
                    
                    if(cmd != null)
                    {
                        try
                        {
                            if(cmd.Content == null)
                            {
                                cmd.Content = "";
                            
                                if(this.debug)
                                {
                                    System.out.println("[INFO] content for "+ cmd.Name + "is null");
                                }
                            }
                            
                            if(cmd.Events == null)
                            {
                                cmd.Events = new Stack();
                            
                                if(this.debug)
                                {
                                    System.out.println("[INFO] events for "+ cmd.Name + "are null");
                                }
                            }
                            
                            // match on content value key & event
                            if((cmd.Content.equals(value))&&(cmd.Events.contains("Loaded")))
                            {
                                if(cmd.Operation!=null)
                                {
                                    if(!cmd.Operation.equals(""))
                                    {
                                        // Handle display
                                        if(cmd.Operation.indexOf("Show")>=0)
                                        {
                                            cmd.Event = "Placement";
                                    
                                            if(cmd.Regexes == null)
                                            {
                                                if(
                                                        (cmd.Item.indexOf("TextBox[")>=0)||
                                                        (cmd.Item.indexOf("TextLabel[")>=0)
                                                    )
                                                {
                                                    componentChanges.put(cmd.Item,cmd.Event);
                                                }
                                            }
                                        }
                                  
                                        // Handle Values
                                        if(cmd.Operation.indexOf("Set")>=0)
                                        {
                                            if(cmd.Regexes == null)
                                            {
                                                if(
                                                        (cmd.Item.indexOf("TextBox[")>=0)||
                                                        (cmd.Item.indexOf("TextLabel[")>=0)
                                                    )
                                                {
                                                    if(cmd.FullfillmentDetails != null)
                                                    {
                                                        componentChanges.put(cmd.Item,"Value:"+cmd.FullfillmentDetails.get("Value"));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                
                                // Handle Regexes
                                if(cmd.Regexes!=null)
                                {
                                    // get regex/fullfillment rules
                                    for(Enumeration re = cmd.Regexes.elements(); re.hasMoreElements();)
                                    {
                                        regex = (String) re.nextElement();
                                        String subRegex = "";
                                        
                                        if(regex.indexOf(",")>=0)
                                        {
                                            regexStart = regex.substring(0,regex.indexOf(","));
                                            
                                            if(regex.indexOf("[")<0)
                                            {
                                                regexEnd = regex.substring(regex.indexOf(",")+1);
                                            }
                                            else
                                            {
                                                regexEnd = regex.substring(regex.indexOf(",")+1,regex.indexOf("["));
                                                subRegex = regex.substring(regex.indexOf("[")+1,regex.indexOf("]"));
                                            }
                                        }
                                        else
                                        {
                                            regexStart = regex;
                                            regexEnd = "";
                                        }
                                        
                                        // reset main match index
                                        matchFrom = 0;
                                        matchCount = 0;
                                        changeValue = "";
                                        
                                        // get regex matches
                                        while(matchFrom>=0)
                                        {
                                            // if component => manage
                                            if(
                                                (cmd.Name.indexOf("TextBoxes")>=0)||
                                                (cmd.Name.indexOf("TextLabels")>=0)
                                            )
                                            {
                                                // create new key
                                                changeKey = "@" + matchCount;
                                                
                                                // do operation
                                                if(regexEnd.length()>0)
                                                {
                                                    if(content.indexOf(regexStart,matchFrom) != -1)
                                                    {
                                                        matchFrom = content.indexOf(regexStart,matchFrom)+regexStart.length();
                                                    }
                                                    else
                                                    {
                                                        matchFrom = -1;
                                                    }
                                                    
                                                    if(matchFrom!=-1)
                                                    {
                                                        matchTo = content.indexOf(regexEnd,matchFrom);
                                                    
                                                        // get value
                                                        changeValue = content.substring(matchFrom,matchTo);
                                                    
                                                        if(!subRegex.equals(""))
                                                        {
                                                            changeValue = changeValue.substring(changeValue.indexOf(subRegex)+1);
                                                        }
                                                        
                                                        matchFrom = matchTo + 1;
                                                    }
                                                }
                                                else
                                                {
                                                    // get value
                                                    if(cmd.Operation.equals("Show"))
                                                    {
                                                        changeValue = "Placement";
                                                    }
                                                    
                                                    if(cmd.Operation.equals("Hide"))
                                                    {
                                                        changeValue = "Removal";
                                                    }
                                                    
                                                    // jump to next regex match
                                                    matchFrom = content.indexOf(regexStart,matchFrom+1);
                                                }
                                                
                                                if(matchFrom!=-1)
                                                {
                                                    // save change
                                                    componentChanges.put(cmd.Item.concat(changeKey),changeValue);
                                                }
                                            }
                                            else// if graphic or link => manage
                                            {
                                                // create new key
                                                changeKey = "@" + matchCount;
                                                
                                                // do operation
                                                if(regexEnd.length()>0)
                                                {
                                                    if(content.indexOf(regexStart,matchFrom)!=-1)
                                                    {
                                                        matchFrom = content.indexOf(regexStart,matchFrom)+regexStart.length();
                                                    }
                                                    else
                                                    {
                                                        matchFrom = -1;
                                                    }
                                                
                                                    if(matchFrom!=-1)
                                                    {
                                                        matchTo = content.indexOf(regexEnd,matchFrom);
                                                    
                                                        // get value
                                                        changeValue = content.substring(matchFrom,matchTo);
                                                    
                                                        if(!subRegex.equals(""))
                                                        {
                                                            changeValue = changeValue.substring(changeValue.indexOf(subRegex)+1);
                                                        }
                                                        
                                                        matchFrom = matchTo + 1;
                                                    }
                                                }
                                                else
                                                {
                                                    if(cmd.Operation.length()>0)
                                                    {
                                                        // get value
                                                        if(cmd.Operation.indexOf("Show")>=0)
                                                        {
                                                            changeValue = "Placement";
                                                        }
                                                        
                                                        if(cmd.Operation.indexOf("Hide")>=0)
                                                        {
                                                            changeValue = "Removal";
                                                        }                                                        
                                                    }
                                                    else
                                                    {
                                                        if(cmd.Name.indexOf("Show")>=0)
                                                        {
                                                            changeValue = "Placement";
                                                        }
                                                        
                                                        if(cmd.Name.indexOf("Hide")>=0)
                                                        {
                                                            changeValue = "Removal";
                                                        }
                                                    }
                                                    
                                                    // jump to next regex match
                                                    matchFrom = content.indexOf(regexStart,matchFrom+1);
                                                }
                                                
                                                if((changeValue.equals("Placement")||(changeValue.equals("Removal"))))
                                                {
                                                    if(matchFrom!=-1)
                                                    {
                                                        // save change
                                                        graphicChanges.put(cmd.Item.concat(changeKey),changeValue);
                                                    }
                                                }
                                                else
                                                {
                                                    if(matchFrom!=-1)
                                                    {
                                                        if(cmd.FullfillmentDetails == null)
                                                        {
                                                            cmd.FullfillmentDetails = new Hashtable();
                                                        }
                                                            
                                                        if(cmd.Name.indexOf("Link")>0)
                                                        {
                                                            cmd.FullfillmentDetails.put("Location"+changeKey,changeValue);
                                                        }
                                                        else
                                                        {
                                                            cmd.FullfillmentDetails.put("Value"+changeKey,changeValue);
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            matchCount++;
                                        }
                                    }
                                }
                            }
                        }
                        catch(Exception e)
                        {
                            if(this.debug)
                            {
                                System.out.println("[ERROR] problems implementing content events:"+e.toString());
                            }
                        }
                    }
                }
                
                //Field f = null;
                
                // actually update components by id
                for(Enumeration uce = componentChanges.keys(); uce.hasMoreElements();)
                {
                    changeKey = (String)uce.nextElement();
                    changeValue = (String) componentChanges.get(changeKey);
                            
                    try
                    {
                        // edit existing
                        if((objIndx=this.guiComponentExists(changeKey))>=0)
                        {
                            // implement change
                            ggd = (GraphicGuiDetails) seComponents.elementAt(objIndx);
                            
                            if(changeKey.indexOf("TextLabel")>=0)
                            {
                                RichTextField f = (RichTextField) ggd.ComponentsObject;
                                
                                if(!changeValue.equals("Placement"))
                                {
                                    if(!changeValue.equals(this.currentLocationKey))
                                    {
                                        if(changeValue.indexOf("Value:")>=0)
                                        {
                                            if(changeValue.indexOf("Clicked")>=0)
                                            {
                                                int indx = this.guiComponentExists(this.currentClickedObj);
                                                if(indx >=0)
                                                {
                                                    // Get ggd object
                                                    GraphicGuiDetails clicked_ggd = (GraphicGuiDetails) seComponents.elementAt(indx);
                                                    
                                                    // Get clicked Text
                                                    RichTextField clicked_rtf = (RichTextField) clicked_ggd.ComponentsObject;
                                                
                                                    f.setText(clicked_rtf.getText());
                                                }
                                            }
                                            else
                                            {
                                                int c_indx = this.appContentExists(changeValue.substring(changeValue.indexOf("Value:")+6));
                                                if(c_indx >=0)
                                                {
                                                    // Get cmd object
                                                    ContentManagementDetails value_cmd = (ContentManagementDetails) seContent.elementAt(c_indx);
                                                    
                                                    // Get clicked Text
                                                    String value_rtf = "";
                                                    
                                                    if(this.currentClickedObj !=null)
                                                    {
                                                        if(!this.currentClickedObj.equals(""))
                                                        {
                                                            value_rtf = (String) value_cmd.FullfillmentDetails.get("Value"+this.currentClickedObj.substring(this.currentClickedObj.indexOf("@")));
                                                        }
                                                        else
                                                        {
                                                            value_rtf = (String) value_cmd.FullfillmentDetails.get("Value");
                                                        }
                                                    }
                                                    else
                                                    {
                                                        value_rtf = (String) value_cmd.FullfillmentDetails.get("Value");
                                                    }
                                                    
                                                    if(value_rtf !=null)
                                                    {
                                                        f.setText(value_rtf);
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            f.setText(changeValue);
                                        }
                                    }
                                }
                                
                                ggd.ComponentsObject = f;
                                ggd.Event = "Placement";
                            }
                            
                            if(changeValue.indexOf("Placement")>=0)
                            {
                                ggd.Event = "Placement";
                            }
                            
                            if(changeValue.indexOf("Removal")>=0)
                            {
                                ggd.Event = "Removal";
                            }
                            
                            seComponents.setElementAt(ggd,objIndx);
                        }
                        else // add new
                        {
                            // add brand new component
                            ggd = new GraphicGuiDetails();
                            
                            if(changeKey.indexOf("TextLabel")>=0)
                            {
                                
                                // assume all width 
                                RichTextField f = new RichTextField(Field.USE_ALL_WIDTH|Field.FIELD_HCENTER|Field.NON_FOCUSABLE);
                                
                                if(!changeValue.equals("Placement"))
                                {
                                    if(!changeValue.equals(this.currentLocationKey))
                                    {   
                                        f.setText(changeValue);
                                    }
                                }
                                
                                ggd.ComponentsObject = f;
                                ggd.Event = "Placement";
                            }
                            
                            // get existing
                            objIndx =  this.guiComponentExists(changeKey.substring(0,changeKey.indexOf("@"))+"0");
                            
                            if(objIndx>=0)
                            {
                                GraphicGuiDetails ggdCopy = (GraphicGuiDetails) seComponents.elementAt(objIndx);
                
                                ggd.Id = ggdCopy.Id;
                                double newX = ggdCopy.PlacementX() + (Double.valueOf(changeKey.substring(changeKey.indexOf("@")+1)).doubleValue() * ggdCopy.xPlacementIncrement);
                                double newY = ggdCopy.PlacementY() + (Double.valueOf(changeKey.substring(changeKey.indexOf("@")+1)).doubleValue() * ggdCopy.yPlacementIncrement);
                                
                                ggd.Placement = newX + ":" + newY;
                                ggd.Name = ggdCopy.Name;
                                ggd.ObjectColor = ggdCopy.ObjectColor;
                                ggd.Events = ggdCopy.Events;
                                ggd.indx = Integer.valueOf(changeKey.substring(changeKey.indexOf("@")+1)).intValue(); 
                                ggd.yPlacementIncrement = ggdCopy.yPlacementIncrement;
                                ggd.xPlacementIncrement = ggdCopy.xPlacementIncrement;
                            
                                seComponents.addElement(ggd);
                            }
                        }
                    }
                    catch(Exception ex)
                    {
                        if(this.debug)
                        {
                            System.out.println("[ERROR] problems updating component with changeKey:"+changeKey);
                        }
                    }
                }
                                    

                // actually update graphics by id
                for(Enumeration uge = graphicChanges.keys(); uge.hasMoreElements();)
                {
                    changeKey = (String)uge.nextElement();
                    changeValue = (String) graphicChanges.get(changeKey);
                            
                    try
                    {
                        // edit existing
                        if((objIndx=this.guiGraphicExists(changeKey))>=0)
                        {
                            // implement change
                            ggd = (GraphicGuiDetails) seGraphics.elementAt(objIndx);
                            
                            if(changeValue.indexOf("Placement")>=0)
                            {
                                // nothing to do really
                                ggd.Event = "Placement";
                            }
                            
                            if(changeValue.indexOf("Removal")>=0)
                            {
                                ggd.Event = "Removal";
                            }
                            
                            
                            seGraphics.insertElementAt(ggd,objIndx);// should be set?
                        }
                        else
                        {
                            // add brand new component
                            ggd = new GraphicGuiDetails();
                            
                            // get existing
                            objIndx =  this.guiGraphicExists(changeKey.substring(0,changeKey.indexOf("@"))+"0");
                            
                            if(objIndx>=0)
                            {
                                GraphicGuiDetails ggdCopy = (GraphicGuiDetails) seGraphics.elementAt(objIndx);
            
                                ggd.Id = ggdCopy.Id;
                                double newX = ggdCopy.PlacementX() + (Double.valueOf(changeKey.substring(changeKey.indexOf("@")+1)).doubleValue() * ggdCopy.xPlacementIncrement);
                                double newY = ggdCopy.PlacementY() + (Double.valueOf(changeKey.substring(changeKey.indexOf("@")+1)).doubleValue() * ggdCopy.yPlacementIncrement);
                            
                                ggd.Placement = newX + ":" + newY;
                                ggd.Name = ggdCopy.Name;
                                ggd.ObjectColor = ggdCopy.ObjectColor;
    
                                if(changeValue.indexOf("Placement")>=0)
                                {
                                    // nothing to do really
                                    ggd.Event = "Placement";
                                }
                                
                                if(changeValue.indexOf("Removal")>=0)
                                {
                                    ggd.Event = "Removal";
                                }
                                //ggd.Event = ggdCopy.Event;
                                
                                ggd.Events = ggdCopy.Events;
                                ggd.Location = ggdCopy.Location;
                                ggd.indx = Integer.valueOf(changeKey.substring(changeKey.indexOf("@")+1)).intValue(); 
                                ggd.GraphicsObject = ggdCopy.GraphicsObject;
                                ggd.xPlacementIncrement = ggdCopy.xPlacementIncrement;
                                ggd.yPlacementIncrement = ggdCopy.yPlacementIncrement;
                                
                                seGraphics.addElement(ggd);
                            }
                        }
                    }
                    catch(Exception Ex)
                    {
                        if(this.debug)
                        {
                            System.out.println("[ERROR] problems updating graphic(s) with changeKey:"+changeKey);
                        }
                    }                        
                }
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems performing operation where event="+event+" and where key="+value+":"+ex.toString());
            }
        }
        
        return true;
    }
    
    public boolean PerformOperationForEvent(Field sf,int event, int index)
    {
        boolean operationPerformed = false;
        RichTextField rtf = null;
        RichTextField asf = null;
        Vector components = new Vector();
        Vector clickEvents = new Vector();
        boolean locationLookup = true;
        boolean indexWithinBounds = false;
        boolean actualValueFound = false;
        boolean isRichTextField = false;
        boolean checkingRichTextField = false;
        double incrementAmount = 0;
        String ev = "";
        int exists = 0;
        
        try
        {
            switch(event)
            {
                case islandSoftwareEngine.USER_DEVICE_SELECTION_EVENT:
                    // load all components out of the seContent with the current key
                    for(int i =0; i< seContent.size(); i++)
                    {
                        // load all click events from component(s)
                        ContentManagementDetails cmd = (ContentManagementDetails) seContent.elementAt(i);
                        
                        if(cmd !=null)
                        {
                            if(cmd.Content!=null)
                            {
                                if(cmd.Content.equals(this.currentLocationKey))
                                {
                                    if(cmd.Operation!=null)
                                    {
                                        // components have set & show 
                                        if(cmd.Operation.equals("Set")||cmd.Operation.equals("Show"))
                                        {
                                            if(components == null)
                                            {
                                                components = new Vector();
                                            }
                                            
                                            if(cmd.Item != null)
                                            {
                                                components.addElement(cmd.Item);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if(components !=null)
                    {
                        // if click event has 'Get' in it - load content event
                        for(int j=0; j<components.size(); j++)
                        {
                            // get all versions of this component
                            for(int a=0; a<seComponents.size(); a++)
                            {
                                GraphicGuiDetails ggd = (GraphicGuiDetails) seComponents.elementAt(a);
                                indexWithinBounds = false;
                                actualValueFound = false;
                                
                                try
                                {
                                    if(ggd!=null)
                                    {
                                        if(ggd.ComponentsObject !=null)
                                        {
                                            if(ggd.ComponentsObject instanceof RichTextField)
                                            {
                                                rtf = (RichTextField) ggd.ComponentsObject;
                                                isRichTextField = true;
                                            }
                                            
                                            if(sf instanceof RichTextField)
                                            {
                                                checkingRichTextField = true;
                                            }
                                        }
                                    
                                        // the right component
                                        if(ggd.Name.equals((String)components.elementAt(j)))
                                        {
                                            asf = (RichTextField) sf;
                                                    
                                            if(asf !=null)
                                            {
                                                if(!asf.getText().equals(""))
                                                {
                                                    locationLookup = false;
                                                }
                                                else
                                                {
                                                    locationLookup = true;
                                                }
                                            }
                                            
                                            if(locationLookup)
                                            {
                                                if(ggd.yPlacementIncrement>0)
                                                {
                                                    incrementAmount = ggd.PlacementY() + ggd.yPlacementIncrement;
                                                }
                                                else
                                                {
                                                    if(ggd.ComponentsObject!=null)
                                                    {
                                                        Field f = (Field)ggd.ComponentsObject;
                                                        incrementAmount = ggd.PlacementY() + f.getContentHeight();
                                                    }
                                                }
                                                
                                                if((index>=ggd.PlacementY())&&(index<incrementAmount))
                                                {
                                                    indexWithinBounds = true;
                                                }
                                            }
                                            else
                                            {
                                                if((isRichTextField)&&(checkingRichTextField))
                                                {
                                                    asf = (RichTextField) sf;
                                                    
                                                    if((asf != null)&&(rtf !=null))
                                                    {
                                                        if((rtf.getText().indexOf(asf.getText())>=0)&&(!asf.getText().equals(""))&&(!rtf.getText().equals("")))
                                                        {
                                                            if(this.debug)
                                                            {
                                                                System.out.println("[INFO] rtf is:"+rtf.getText()+"--"+asf.getText());
                                                            }
                                                                                                                                                                                          
                                                            actualValueFound = true;
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            
                                            if(indexWithinBounds||actualValueFound)
                                            {
                                                if(ggd.Events !=null)
                                                {
                                                    for(Enumeration e = ggd.Events.elements(); e.hasMoreElements();)
                                                    {
                                                        ev = (String) e.nextElement();
                                                        
                                                        if(ev.indexOf("Click")>=0)
                                                        {
                                                            if(clickEvents == null)
                                                            {
                                                                clickEvents = new Vector();
                                                            }
                                                            
                                                            // Add Event
                                                            clickEvents.addElement(ev.substring(ev.indexOf(":")+1)+"@"+String.valueOf(ggd.indx).toString());
                                                            
                                                            this.currentClickedObj = ggd.Name + "@" + ggd.indx;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                catch(Exception exc)
                                {
                                    if(this.debug)
                                    {
                                        System.out.println("[ERROR] problems handling ggd for Event:"+exc.toString());
                                    }
                                }
                            }
                        }
                    }
                    
                    if(clickEvents!=null)
                    {
                        for(Enumeration en = clickEvents.elements(); en.hasMoreElements();)
                        {
                            String actionKey = (String)en.nextElement();
                            
                            // if event value has 'Get' in it - load content event
                            if(actionKey.indexOf("Get")>=0)
                            {
                                if((exists = this.appContentExists(actionKey))>=0)
                                {
                                    ContentManagementDetails cmd  = (ContentManagementDetails) seContent.elementAt(exists);
                                    
                                    if(cmd!=null)
                                    {
                                        // get content man Key ? by translator?
                                        //if(this.translatorPoints.elements(new Integer(islandSoftwareEngine.APP_CONTENT_MANAGEMENT)))
                                        {
                                            for(Enumeration enm = this.translatorPoints.elements(new Integer(islandSoftwareEngine.APP_CONTENT_MANAGEMENT)); enm.hasMoreElements();)
                                            {
                                                Object contentObj = (Object) enm.nextElement();
                                             
                                                if(contentObj instanceof ContentManagementDetails)
                                                {
                                                    //ContentManagementDetails evt_cmd = (ContentManagementDetails) seContent.elementAt(exists);
                                                    ContentManagementDetails evt_cmd = (ContentManagementDetails) contentObj;
                                                    
                                                    if(evt_cmd.Name.indexOf(cmd.Content)>=0)
                                                    {
                                                        if(evt_cmd.FullfillmentDetails!=null)
                                                        {
                                                            for(Enumeration enu=evt_cmd.FullfillmentDetails.keys(); enu.hasMoreElements();)
                                                            {
                                                                String managementKey = (String)enu.nextElement();
                                                                
                                                                // if event has 'Location' in it - load Kernel network , set download location if http
                                                                if(managementKey.indexOf("Location")>=0)
                                                                {
                                                                    try
                                                                    {
                                                                        // set need outside help flag
                                                                        this._needsExternalEvent = true;
                                                                        
                                                                        this.downloadLocationKey = (String)evt_cmd.FullfillmentDetails.get(managementKey);
                                                                        
                                                                        this.downloadManagementKey = evt_cmd.Name;
                                                                        
                                                                        if(!evt_cmd.Operation.equals(""))
                                                                        {
                                                                            this.downloadTravelMethod = evt_cmd.Operation;
                                                                        }
                                                                        else
                                                                        {
                                                                            this.downloadTravelMethod = evt_cmd.Name.substring(0,evt_cmd.Name.indexOf("-"));
                                                                        }
                                                                        
                                                                        // look up the actual remote location/variable for this
                                                                        String fullDownloadKey = (String)this.remoteAccessPoints.get(downloadLocationKey);
                                                                        
                                                                        this.downloadContentLocation = getKernelNetworkLocation(fullDownloadKey+actionKey.substring(actionKey.indexOf("@")));
                                                                        
                                                                        //this.getKernelNetworkLocation(this.downloadLocationKey);
                                                                        if((this.downloadContentLocation.indexOf("http://")<0)&&(this.downloadTravelMethod.indexOf("Get")>=0))
                                                                        {
                                                                            this.downloadContentLocation = "http://" + 
                                                                                downloadLocationKey.substring(0,downloadLocationKey.indexOf("[")) + 
                                                                                "/" +
                                                                                this.downloadContentLocation;
                                                                                
                                                                            if(this.debug)
                                                                            {
                                                                                System.out.println("[INFO] download location is:"+this.downloadContentLocation);
                                                                            }
                                                                        }
                                                                    }
                                                                    catch(Exception excep)
                                                                    {
                                                                        if(this.debug)
                                                                        {
                                                                            System.out.println("[ERROR] problems getting download Location: "+excep.toString());
                                                                        }
                                                                    }
                                                                    
                                                                    // set state
                                                                    this.currentState = islandSoftwareEngine.KERNEL_NETWORK;
                                                                    
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
                            
                            if(actionKey.indexOf("Show")>=0)
                            {
                                this.PerformOperationForEvent("Loaded",actionKey.substring(actionKey.indexOf("-")+1,actionKey.indexOf("@")),this.downloadResultContent);
                            }
                        }
                    }
                    
                    break;
                
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems performing operation for event:"+e.toString());
            }
        }
        
        return true;
   }
    
   public boolean PerformOperationForEvent(int event, String value)
    {
        boolean operationPerformed = false;
        String ev = "";
        
        try
        {
            switch(event)
            {
                case islandSoftwareEngine.USER_DEVICE_KEY_EVENT:
                    break;
                
                case islandSoftwareEngine.USER_DEVICE_MENU_EVENT:
                    
                        
                    // look in grid events
                    for(Enumeration e=seGrids.elements(); e.hasMoreElements();)
                    {
                        GridScreenDetails gsd = (GridScreenDetails) e.nextElement();
                    
                        if(gsd!=null)
                        {
                            for(Enumeration en = gsd.Events.elements(); en.hasMoreElements();)
                            {
                                String screenEvent = (String) en.nextElement();
                                
                                if((screenEvent.indexOf("Load_")>=0)&&(screenEvent.indexOf(value)>=0))
                                {
                                    // Load or change screen name
                                    gsd.ScreenName = screenEvent.substring(screenEvent.indexOf("_")+1);
                                }
                                
                                if((screenEvent.indexOf("Init_")>=0)&&(screenEvent.indexOf(value)>=0))
                                {
                                    // Load or change screen name
                                    gsd.ScreenName = screenEvent.substring(screenEvent.indexOf("_")+1);
                                }
                            }
                        }
                    }
                    
                    break;
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems performing event operation:"+ex.toString());
            }
        }
        
        return operationPerformed;
    }
    
    public boolean PerformOperationForEvent(int event, char value)
    {
        boolean operationPerformed = false;
        String ev = "";
        
        try
        {
            switch(event)
            {
                case islandSoftwareEngine.USER_DEVICE_KEY_EVENT:
                    // look for Write Event in Components
                    for(Enumeration e = seComponents.elements(); e.hasMoreElements();)
                    {
                        GraphicGuiDetails ggd = (GraphicGuiDetails) e.nextElement();
                        
                        if(ggd != null)
                        {
                            if(ggd.Events !=null)
                            {
                                for(Enumeration f = ggd.Events.elements(); f.hasMoreElements();)
                                {
                                    ev = (String )f.nextElement();
                                    
                                    if(ev.indexOf("Write")>=0)
                                    {
                                        if(ggd.ComponentsObject instanceof BasicEditField)
                                        {
                                            BasicEditField bef = (BasicEditField) ggd.ComponentsObject;
                                            
                                            // add & write
                                            bef.setText(bef.getText()+value);
                                            
                                            ggd.ComponentsObject = bef;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if(ggd.Event!=null)
                                {
                                    ev = (String) ggd.Event;
                                    
                                    if(ev.indexOf("Write")>=0)
                                    {
                                        if(ggd.ComponentsObject instanceof BasicEditField)
                                        {
                                            BasicEditField bef = (BasicEditField) ggd.ComponentsObject;
                                            
                                            // add & write
                                            bef.setText(bef.getText()+value);
                                            
                                            ggd.ComponentsObject = bef;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                     // look for Write Event in Graphics
                    /*
                    for(Enumeration e = )
                    {
                        
                    }
                    */
                    break;
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems performing event operation:"+ex.toString());
            }
        }
        
        
        return operationPerformed;
    }    
    
    public boolean PerformOperationForEvent(int event)
    {
        boolean operationPerformed = false;
        String ev = "";
        //String val = new String(value);
        
        try
        {
            switch(event)
            {
                case islandSoftwareEngine.USER_DEVICE_KEY_EVENT:
                    // look for Write Event in Components
                    for(Enumeration e = seComponents.elements(); e.hasMoreElements();)
                    {
                        GraphicGuiDetails ggd = (GraphicGuiDetails) e.nextElement();
                        
                        if(ggd != null)
                        {
                            if(ggd.Events !=null)
                            {
                                for(Enumeration f = ggd.Events.elements(); f.hasMoreElements();)
                                {
                                    ev = (String )f.nextElement();
                                    
                                    if(ev.indexOf("Write")>=0)
                                    {
                                        if(ggd.ComponentsObject instanceof BasicEditField)
                                        {
                                            BasicEditField bef = (BasicEditField) ggd.ComponentsObject;
                                            
                                            // add & write
                                            //bef.setText(bef.getText());
                                            
                                            ggd.ComponentsObject = bef;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if(ggd.Event!=null)
                                {
                                    ev = (String) ggd.Event;
                                    
                                    if(ev.indexOf("Write")>=0)
                                    {
                                        if(ggd.ComponentsObject instanceof BasicEditField)
                                        {
                                            BasicEditField bef = (BasicEditField) ggd.ComponentsObject;
                                            
                                            // add & write
                                            //bef.setText(bef.getText());
                                            
                                            ggd.ComponentsObject = bef;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                     // look for Write Event in Graphics
                    /*
                    for(Enumeration e = )
                    {
                        
                    }
                    */
                    
                    break;
                case islandSoftwareEngine.USER_DEVICE_SELECTION_EVENT:
                    // repaint graphics
                    
                    // hanlde components click event 
                    
                    // hanlde click with content init event
                    
                    // handle content init event w/ kernel if necessary
                    
                    // handle click with kernel init event
                    break;
                case islandSoftwareEngine.APP_CONTENT_EVENT:
                    break;
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems performing event operation:"+ex.toString());
            }
        }
        
        
        return operationPerformed;
    }
    
    public boolean PerformTranslationForFunction()
    {
        boolean translationPerformed = false;
        
        return translationPerformed;
    }

    public boolean HasLayouts()
    {
        return this._hasLayouts;
    }
    
    public boolean HasGraphics()
    {
        return this._hasGraphics;
    }
    
    public boolean HasComponents()
    {
        return this._hasComponents;
    }
    
    // =====================Private methods================>
    /**
     * Function returns the id for a given gui map value.
     * Gui component values are always contained in [].
     * @return <description>
     */
    private String getGuiId(String guiMapValue)
    {
        String id = "";
        Integer i = null;
        
        if(guiMapValue.indexOf("_")>=0)
        {
            if((guiMapValue.indexOf("[")>=0)&&(guiMapValue.indexOf("]")>=0))
            {
                if(guiMapValue.indexOf(".")>=0)
                {
                    i = Integer.valueOf(guiMapValue.substring(guiMapValue.indexOf("[")+1,guiMapValue.indexOf(".")));
                    id = i.toString();
                }
                else
                {
                    i = Integer.valueOf(guiMapValue.substring(guiMapValue.indexOf("[")+1,guiMapValue.indexOf("]")));
                    id = i.toString();
                }
            }
        }
        else
        {
            if((guiMapValue.indexOf("[")>=0)&&(guiMapValue.indexOf("]")>=0))
            {
                if(guiMapValue.indexOf(".")>=0)
                {
                    i = Integer.valueOf(guiMapValue.substring(guiMapValue.indexOf("[")+1,guiMapValue.indexOf(".")));
                    id = i.toString();
                }
                else
                {
                    i = Integer.valueOf(guiMapValue.substring(guiMapValue.indexOf("[")+1,guiMapValue.indexOf("]")));
                    id = i.toString();
                }
            }
        }
        
        return id;
    }
    
    private String getGuiLayoutId(String guiMapValue)
    {
        String id = "";
        Integer i = null;
        
        if(guiMapValue.indexOf("_")>=0)
        {
            if((guiMapValue.indexOf("[")>=0)&&(guiMapValue.indexOf("]")>=0))
            {
                if(guiMapValue.indexOf(".")>=0)
                {
                    i = Integer.valueOf(guiMapValue.substring(guiMapValue.indexOf(".")+1,guiMapValue.indexOf("]")));
                    id = i.toString();
                }
                else
                {
                    i = Integer.valueOf(guiMapValue.substring(guiMapValue.indexOf("[")+1,guiMapValue.indexOf("]")));
                    id = i.toString();
                }
            }
        }
        
        return id;
    }
    
    private String getGuiLayoutName(String key)
    {
        
        String layoutName = "";
        
        if(key.indexOf("[")>=0)
        {
            if(key.indexOf(".")>=0)
            {
                DisplayLayoutDetails dld = (DisplayLayoutDetails) seLayouts.elementAt(Integer.parseInt(key.substring(key.indexOf(".")+1,key.indexOf("]"))));
                layoutName = dld.Name;
            }
        }
        
        return layoutName;
    }


    /**
     * Function gives Component for label.
     * There is a text to object mapping that will
     * take place here.
     * @param guiMapValue <description>
     * @return <description>
     */
    private Object getGuiComponent(String guiMapValue)
    {
        Object compObj = null;
        
        // TextField=>BasicEditField
        if(guiMapValue.indexOf("TextBox")>=0)
        {
            compObj = new BasicEditField(Field.USE_ALL_WIDTH|Field.FIELD_HCENTER|Field.NON_FOCUSABLE);
        }

        // TextLabel=>RichTextField
        if(guiMapValue.indexOf("TextLabel")>=0)
        {
            compObj = new RichTextField(Field.USE_ALL_WIDTH);
        }
        
        // BmpImage=>BitmapField
        if(guiMapValue.indexOf("BmpImage")>=0)
        {
            compObj = new BitmapField();
        }
        
        // Button=>ButtonField
        if(guiMapValue.indexOf("Button")>=0)
        {
            compObj = new ButtonField();
        }
        
        // Checkbox=>CheckboxField
        if(guiMapValue.indexOf("Checkbox")>=0)
        {
            compObj = new CheckboxField();
        }
        return compObj;
    }
    
    /**
     * Function gives just the name for what we are 
     * going to use in the Details object.
     * @return <description>
     */
    private String getGuiComponentName(String guiMapValue)
    {
        String compName = "";
        String key = guiMapValue;
        int i;
        
        if(key.indexOf("_")>=0)
        {
            compName = key.substring(0,key.indexOf("_"));
            
        }
        
        return compName;
    }
    
    private String getGuiGraphicName(String guiMapValue)
    {
        String graphicName = "";
        String key = guiMapValue;
        int i;
        
        if(key.indexOf("_")>=0)
        {
            graphicName = key.substring(0,key.indexOf("_"));
            
        }
        else
        {
            graphicName = key;
        }
        
        return graphicName;
    }
    
    /**
     * Look in the appropriate vector for the object 
     * you are looking for. Graphics are ordered by
     * Icon[0], Background[1], Images[2-?], Text[?-?] 
     * so far starting at vector index 2
     * @param key <description>
     * @return <description>
     */
    private int guiGraphicExists(String key)
    {
        GraphicGuiDetails ggd = null;
        int i = -1, j=0, indx = 0;
        
        try
        {
            if(seGraphics.size()>0)
            {
                if(key!=null)
                {
                    if(key.indexOf("[")>=0)
                    {
                        if(key.indexOf(".")>=0)
                        {
                            i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("."))).intValue();
                        }
                        else
                        {                            
                            i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("]"))).intValue();
                        }
                        
                        if(this.debug)
                        {
                            //System.out.println("this is i:"+i);
                        }
                        
                        if(seGraphics.size()>i)
                        {
                            for(j=0;j<seGraphics.size();j++)
                            {
                                ggd = (GraphicGuiDetails) seGraphics.elementAt(j);
    
                                if(ggd !=null)
                                {
                                    if(Integer.valueOf(ggd.Id).intValue() ==i)
                                    {
                                        if(key.indexOf("@")>=0)
                                        {
                                            indx = Integer.valueOf(key.substring(key.indexOf("@")+1)).intValue();
                                                    
                                            if(ggd.indx == indx)
                                            {
                                                i = j;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            i = j;
                                            break;
                                        }
                                    }
                                 }
                             }
                        }
                        else
                        {
                            i = -1;
                        }
                    }
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting existing graphic details:"+e.toString());
            }
        }
        
        if(j!=i)
        {
            i = -1;
        }
        
        return i;
    }
    
    /**
     * <description>
     * @param key <description>
     * @return <description>
     */
    private int guiComponentExists(String key)
    {
        GraphicGuiDetails ggd = null;
        int i = -1;
        int indx = 0;
        int a = 0;
        
        try
        {
            if(seComponents.size()>0)
            {
                if(key!=null)
                {
                    if(key.indexOf("[")>=0)
                    {
                        if(key.indexOf(".")>=0)
                        {
                            i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("."))).intValue();
                        }
                        else
                        {
                            i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("]"))).intValue();
                        }
                        
                        if(this.debug)
                        {
                            System.out.println("this is i:"+i);
                        }
                        
                        if(seComponents.size()>i)
                        {
                            
                            // search for the match
                            for(a=0; a<seComponents.size(); a++)
                            {
                                ggd = (GraphicGuiDetails) seComponents.elementAt(a);
                                    
                                if(ggd !=null)
                                {
                                    // does the id match
                                    if(Integer.valueOf(ggd.Id).intValue() == i)
                                    {
                                        // get the index
                                        if(key.indexOf("@")>=0)
                                        {
                                            indx = Integer.valueOf(key.substring(key.indexOf("@")+1)).intValue();
                                                
                                            // does te index match
                                            if(ggd.indx == indx)
                                            {
                                                    i = a;
                                                    // done!
                                                    break;
                                            }
                                         }
                                         else
                                         {
                                            i = a;
                                            break;
                                         }
                                    }
                                }
                            }
                        }
                        else
                        {
                            i = -1;
                        }
                    }
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting existing component details:"+e.toString());
            }
        }
        
        if(i!=a)
        {
            i =-1;
        }
        
        return i;
    }
    
    private int gridLayoutExists(String key)
    {
        int layoutIndx = -1;
        String Id = "";
        String layoutName = "";
        
        if(seLayouts.size()>0)
        {
            // if there is an index get it
            if(key.indexOf("[")>=0)
            {
                if(key.indexOf(".")>=0)
                {
                    Id = key.substring(key.indexOf("[")+1,key.indexOf("."));
                }
                else
                {
                    Id = key.substring(key.indexOf("[")+1,key.indexOf("]"));
                }
                    
                if(seLayouts.size()>Integer.parseInt(Id))
                {
                    layoutIndx = Integer.parseInt(Id);
                }
                
            }
            else
            {
                layoutName = key.substring(0,key.indexOf("_"));
                
                // loop through the existing grids
                for(Enumeration en = seLayouts.elements(); en.hasMoreElements();)
                {
                    DisplayLayoutDetails dld  = (DisplayLayoutDetails) en.nextElement();
                
                    if(dld.Name.equals(layoutName))
                    {
                        layoutIndx = dld.indx;
                    }
                }
            }   
        }
        
        return layoutIndx;    
   }

    private int gridExists(String key)
    {
        int gridIndx = -1;
        String Id = "";
        String gridName = "";
        
        if(seGrids.size()>0)
        {
            // if there is an index get it
            if(key.indexOf("[")>=0)
            {
                Id = key.substring(key.indexOf("[")+1,key.indexOf("]"));
                if(seGrids.size()>Integer.parseInt(Id))
                {
                    gridIndx = Integer.parseInt(Id);
                }
            }
            else
            {
                gridName = key.substring(0,key.indexOf("_"));
                
                // loop through the existing grids
                for(Enumeration en = seGrids.elements(); en.hasMoreElements();)
                {
                    GridScreenDetails gsd  = (GridScreenDetails) en.nextElement();
                
                    if(gsd.GridName.equals(gridName))
                    {
                        gridIndx = gsd.GridIndex;
                    }
                }
            }   
        }
        
        return gridIndx;
    }   

    private String getGuiPlacement(String key)
    {
        String placement = "";
        int i= 0;
        
        if(key.indexOf("_")>=0)
        {
            placement = key.substring(key.indexOf("_")+1);
            
        }
        
        return placement;
    }

    private String getGuiPlacements(String key)
    {
        String placement = "";
        int i= 0;
        
        if(key.indexOf("_")>=0)
        {
            placement = key.substring(key.indexOf("_")+1);
            
        }
        
        return placement;
    }

    private String getGuiLocation(String key)
    {
        String location = "";
        int i = 0;
        
        try
        {
            if(key.indexOf("_")>=0)
            {
                location = key.substring(key.indexOf("_")+1);
                
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting gui location:"+ex.toString());
            }
        }
        
        return location;
    }
    
    private int getObjectColor(String key)
    {   
        int objColor = Color.BLACK;
        String color = "";
        
        if(key.indexOf(":")>=0)
        {
            color = key.substring(key.indexOf(":")+1);
            
            if(color.equals("White"))
            {
                objColor = Color.WHITE;
            }
        }
        
        return objColor;
    }
    private String getKernelEvent(String key)
    {
        
        return "";
    }
    
    private String getContentEvent(String key)
    {
        String event = "";
        int i = 0;
        
        try
        {
            if(key.indexOf("_")>=0)
            {
                event = key.substring(key.indexOf("_")+1);
                
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting gui Event from key:"+ex.toString());
            }
        }
        
        return event;
    }
    
    /**
     * This function sets the default event value.
     * This is for use later when processing events
     * on the object itself.
     * @param key <description>
     * @return <description>
     */
    private String getGuiEvent(String key)
    {
        String event = "";
        int i = 0;
        
        try
        {
            if(key.indexOf("_")>=0)
            {
                event = key.substring(key.indexOf("_")+1);
                
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting gui Event from key:"+ex.toString());
            }
        }
        
        return event;
    }
    
    /**
     * This function handles events that can
     * be acted on now. Init at the moment,
     * but there may be others in the future.
     * @param key <description>
     * @param obj <description>
     * @return <description>
     */
    private Object getGuiEvent(String key, Object obj)
    {
        Object newObj = null;
        int i = 0;
        
        try
        {
            if(key.indexOf("_")>=0)
            {
                if(key.indexOf("Init")>=0)
                {
                    // TextBox=>BasicEditField
                    if(key.indexOf("TextBox")>=0)
                    {
                        BasicEditField bef = (BasicEditField)obj;
                        bef.setText(key.substring(key.indexOf(":")+1));
                        newObj = bef;
                    }
            
                    // TextLabel=>RichTextField
                    if(key.indexOf("TextLabel")>=0)
                    {
                        RichTextField rtf = (RichTextField)obj;
                        rtf.setText(key.substring(key.indexOf(":")+1));
                        newObj = rtf;
                    }
                    
                    // Button=>ButtonField
                    if(key.indexOf("Button")>=0)
                    {
                        ButtonField bf = (ButtonField)obj;
                        bf.setLabel(key.substring(key.indexOf(":")+1));
                        newObj = bf;
                    }
                    
                    // Checkbox=>CheckboxField
                    if(key.indexOf("Checkbox")>=0)
                    {
                        CheckboxField cbf = (CheckboxField)obj;
                        if(key.substring(key.indexOf(":")+1).equals("True"))
                        {
                            cbf.setChecked(true);
                        }
                        else
                        {
                            cbf.setChecked(false);
                        }
                        
                        newObj = cbf;
                    }
                }   
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting/handling gui event:"+ex.toString());
            }
        }
        return newObj;
    }
    
    private void setLoginDetails(String key)
    {
        // check format
        if(key.indexOf("-")>=0)
        {
            // check Init: event
            if(key.indexOf(":Init")>=0)
            {
                // Set travel method
                this.loginTravelMethod = key.substring(key.indexOf("_")+1,key.indexOf("-"));
                
                // Set Management Key
                this.loginManagementKey = key.substring(key.indexOf("-")+1,key.indexOf(":"));
            }
        }
    }

    private String getKernelAddressLocation(String key)
    {
        return "";
    }
    
    private String getKernelNetworkLocation(String key)
    {
        String url = "";
        
        // set protocol
        if(key.indexOf("HTTP")>=0)
        {
            url = "http://";
        
            // if a straight value
            if(key.indexOf(".com")>=0)
            {
                url += key.substring(0,key.indexOf("["));
                
                if(key.indexOf(":")>=0)
                {
                    url += key.substring(key.indexOf(":")+1);
                }
            }
            else // referenced value
            {
                // look up content event by currentContentKey
                for(int i=0; i<seContent.size();i++)
                {
                    ContentManagementDetails cmd = (ContentManagementDetails) seContent.elementAt(i);
                    // if Set
                    if(cmd != null)
                    {
                        //if(cmd.Content.indexOf(this.currentLocationKey)>=0)
                        {
                            if(cmd.Item!=null)
                            {
                                String item = key.substring(key.indexOf(":")+1);
                                if((item.indexOf(cmd.Item)>=0)&&(!cmd.Item.equals("")))
                                {
                                    if(cmd.FullfillmentDetails !=null)
                                    {
                                        url = (String)cmd.FullfillmentDetails.get("Location"+key.substring(key.indexOf("@")));
                                        if(url !=null)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            if(url.length()>7)
            {
                // update internal remote site map
                if(this.remoteAccessPoints == null)
                {
                    this.remoteAccessPoints = new Hashtable();
                }
                
                if(key.indexOf("HTTP:/")>=0)
                {
                    this.remoteAccessPoints.put(key.substring(0,key.indexOf("_")),url);
                }
                else
                {
                    if(key.indexOf("_")>=0)
                    {
                        this.remoteAccessPoints.put(key.substring(0,key.indexOf("_")),key.substring(key.indexOf("_")+1));
                    }
                }
            }
        }
       
        return url;
    }
    
    private String getContentValue(String key)
    {
        String value = "";
        if(key.indexOf("_")>=0)
        {
            if(key.indexOf(":")>=0)
            {
                value = key.substring(key.indexOf("_")+1,key.indexOf(":"));
            }

        }
        
        return value;
    }
    
    private int appContentExists(Object obj)
    {
        ContentManagementDetails cmd = null;
        String key;
        int i = -1;
        int j= 0;
        
        try
        {
            if(seContent.size()>0)
            {
                if(obj!=null)
                {
                    if(obj instanceof String)
                    {
                        key = (String)obj;
                        
                        if(key.indexOf("[")>=0)
                        {
                            i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("]"))).intValue();
                        
                            if(this.debug)
                            {
                                System.out.println("this is i:"+i);
                            }
                        }
                    }
                    
                    if(obj instanceof ContentManagementDetails)
                    {
                        cmd = (ContentManagementDetails)obj;
                        
                        i = Integer.valueOf(cmd.Id).intValue();
                        
                        if(this.debug)
                        {
                            System.out.println("this is i:"+i);
                        }
                    }
                        
                    if(seContent.size()>i)
                    {
                        cmd = (ContentManagementDetails) seContent.elementAt(i);
                                
                        if(cmd == null)
                        {
                            for(j=0;j<seContent.size();j++)
                            {
                                cmd = (ContentManagementDetails) seContent.elementAt(j);
    
                                if(cmd !=null)
                                {
                                    if(Integer.valueOf(cmd.Id).intValue() ==i)
                                    {
                                        i = j;
                                        break;
                                    }
                                }
                            }
                                    
                            if(cmd == null)
                            {
                                i = -1;
                            }
                            else
                            {
                                if(Integer.valueOf(cmd.Id).intValue() !=i)
                                {
                                            if(i!=j)
                                            {
                                                i = -1;
                                            }
                                 }
                            } 
                        }
                        else
                        {
                            if(Integer.valueOf(cmd.Id).intValue() !=i)
                            {
                                for(j=0;j<seContent.size();j++)
                                {
                                    cmd = (ContentManagementDetails) seContent.elementAt(j);
                                            
                                    if(cmd !=null)
                                    {
                                        if(Integer.valueOf(cmd.Id).intValue() ==i)
                                        {
                                            i = j;
                                            break;
                                        }
                                     }
                                }
                                        
                                if(cmd == null)
                                {
                                    i = -1;
                                }
                                else
                                {
                                    if(Integer.valueOf(cmd.Id).intValue() !=i)
                                    {
                                        if(i!=j)
                                        {
                                            i = -1;
                                        }
                                    } 
                                }
                            }
                        }
                    }
                    else
                    {
                        i = -1;
                    }
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting existing content details:"+e.toString());
            }
        }
    
        return i;
    }
    
    private int appContentExists(Object obj, int start)
    {
        ContentManagementDetails cmd = null;
        String key;
        int i = -1;
        int j = 0;
        
        try
        {
            if(seContent.size()>0)
            {
                if(obj!=null)
                {
                    if(obj instanceof String)
                    {
                        key = (String)obj;
                        
                        if(key.indexOf("[")>=0)
                        {
                            i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("]"))).intValue();
                        
                            if(this.debug)
                            {
                                System.out.println("this is i:"+i);
                            }
                        }
                    }
                    
                    if(obj instanceof ContentManagementDetails)
                    {
                        cmd = (ContentManagementDetails)obj;
                        
                        i = Integer.valueOf(cmd.Id).intValue();
                        
                        if(this.debug)
                        {
                            System.out.println("this is i:"+i);
                        }
                    }
                        
                    if(seContent.size()>i)
                    {
                        for(j=start;j<seContent.size();j++)
                        {
                            cmd = (ContentManagementDetails) seContent.elementAt(j);
    
                            if(cmd !=null)
                            {
                                if(Integer.valueOf(cmd.Id).intValue() ==i)
                                {
                                    i = j;
                                    break;
                                }
                            }
                        }
                    }
                    else
                    {
                        i = -1;
                    }
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting existing content details:"+e.toString());
            }
        }
    
        if(i != j)
        {
            i = -1;
        }
    
        return i;
    }
    
    private String getContentName(String key)
    {
        String contentName = "";
        int i;
        
        if(key.indexOf("_")>=0)
        {
            contentName = key.substring(0,key.indexOf("_"));
            
        }
        else
        {
            contentName = key;
        }
        
        return contentName;
        //return "";
    }
   
    private String getContentId(String key)
    {
        String id = "";
        Integer i = null;
        
        if(key.indexOf("_")>=0)
        {
            if((key.indexOf("[")>=0)&&(key.indexOf("]")>=0))
            {
                i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("]")));
                id = i.toString();
            }
        }
        else
        {
            if((key.indexOf("[")>=0)&&(key.indexOf("]")>=0))
            {
                i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("]")));
                id = i.toString();
            }
        }
        
        return id;
    }
    
    private String getGridLayoutId(String key)
    {
        String id = "";
        Integer i = null;
        
        if(key.indexOf("_")>=0)
        {
            if((key.indexOf("[")>=0)&&(key.indexOf("]")>=0))
            {
                if(key.indexOf(".")>=0)
                {
                    i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf(".")));
                    id = i.toString();
                }
                else
                {
                    i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("]")));
                    id = i.toString();
                }
            }
        }
        else
        {
            if((key.indexOf("[")>=0)&&(key.indexOf("]")>=0))
            {
                if(key.indexOf(".")>=0)
                {
                        i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf(".")));
                        id = i.toString();
                }
                else
                {
                        i = Integer.valueOf(key.substring(key.indexOf("[")+1,key.indexOf("]")));
                        id = i.toString();
                }
            }
        }
    
        return id;
    }
    
    /**
     * Function will return the grid index for a 
     * given mapping key. Will handle the '.'
     * notation and regular [] index format.
     * @param key <description>
     * @return <description>
     */
    private String getGridId(String key)
    {
        String gridIndx = "";
        
        // must be init grid label or layout
        if(key.indexOf("[")>=0)
        {
            // must be layout
            if(key.indexOf(".")>=0)
            {
                gridIndx = key.substring(key.indexOf(".")+1,key.indexOf("]"));
            }
            else
            {
                // init grid label 
                gridIndx = key.substring(key.indexOf("[")+1,key.indexOf("]"));
            }
        }
        else
        {
            if(seGrids.size() >0)
            {
                for(Enumeration e = seGrids.elements(); e.hasMoreElements();)
                {
                    GridScreenDetails gsd = (GridScreenDetails) e.nextElement();
                
                    if(key.indexOf("_")>=0)
                    {
                        if(gsd.GridName.equals(key.substring(0,key.indexOf("_"))))
                        {
                            gridIndx = Integer.toString(gsd.GridIndex);
                        }
                    }
                    else
                    {
                        gridIndx = Integer.toString(gsd.GridIndex);
                    }
                }
            }
            else
            {
                gridIndx = "0";
            }
        }
        
        return gridIndx;
    }
    
    private String getGridLayoutOS(String key)
    {
        String os = "";
        
        if(key.indexOf("_")>=0)
        {
            os = key.substring(key.indexOf("_")+1, key.indexOf(":"));
        }
        
        return os;
    }
    
    private String getGridLayoutVersion(String key)
    {
        String version = "";
        
        if(key.indexOf(":")>=0)
        {
            version = key.substring(key.indexOf(":")+1);
        }
        
        return version;
    }
    
    private String getGridLayoutName(String key)
    {
        String layoutName = "";
        int i;
        
        if(key.indexOf("_")>=0)
        {
            layoutName = key.substring(0,key.indexOf("_"));
            
        }
        else
        {
            layoutName = key;
        }
        
        return layoutName;
    }
    
    /**
     * Function Gets the grid Name from
     * Grid labels and Layout labels.
     * @param key <description>
     * @return <description>
     */
    private String getGridName(String key)
    {
        String gridName = "";
        
        if(key.indexOf("[")>=0)
        {
            if(key.indexOf(".")>=0)
            {
                GridScreenDetails gsd = (GridScreenDetails) seGrids.elementAt(Integer.parseInt(key.substring(key.indexOf(".")+1,key.indexOf("]"))));
                gridName = gsd.GridName;
            }
            else // must be grid label
            {
                gridName = key.substring(0,key.indexOf("["));
            }
        }
        else
        {
            gridName= key.substring(0,key.indexOf("_"));
            
        }
        
        return gridName;
    }
    
    public GridScreenDetails GetDisplayGridFromName(String gridName)
    {
        
        int gridIndx = Integer.parseInt(this.getGridId(gridName));
        
        return (GridScreenDetails) seGrids.elementAt(gridIndx);
    }
   
    public GridScreenDetails GetDisplayGridFromIndx(int gridIndx)
    {
        
        return (GridScreenDetails) seGrids.elementAt(gridIndx);
    }
    
    public void FlattenGraphicPlacements()
    {
        Double placementX, placementY;
        Double dNewInt;
        String x,y,tmpPlacement;
        boolean needsFlattening = false;
        double fractionValueX = 0;
        double fractionValueY = 0;
        double newInt = 0;
        int divisor = 10;
        
        if(seGraphics == null)
        {
            seGraphics = new Vector();
        }
        
        // the seGraphics
        for(Enumeration e= seGraphics.elements(); e.hasMoreElements();)
        {
            GraphicGuiDetails ggd = (GraphicGuiDetails) e.nextElement();
            
            if(ggd!=null)
            {
                tmpPlacement = ggd.Placement;
                try
                {
                    
                    fractionValueX = ggd.PlacementX()/divisor; // precision ?
                    placementX = new Double(ggd.PlacementX());
                    
                    if(fractionValueX>0)
                    {
                        newInt = placementX.intValue()+fractionValueX;
                        dNewInt = new Double(newInt);
                        x = new String(dNewInt.toString());
                        //ggd.Placement = x;
                        tmpPlacement = x;
                    }
                    else
                    {   
                        newInt = placementX.intValue();
                        dNewInt = new Double(newInt);
                        x = new String(dNewInt.toString());
                        //ggd.Placement = x;
                        tmpPlacement = x;
                    }
                
                    if(tmpPlacement.indexOf(":")<0)
                    {
                        tmpPlacement += ":";
                    }
                
                    fractionValueY = ggd.PlacementY()/divisor; // precision ?
                    placementY = new Double(ggd.PlacementY());
                
                    if(fractionValueY>0)
                    {
                        newInt = placementY.intValue()+fractionValueY;
                        dNewInt = new Double(newInt);
                        y = new String(dNewInt.toString());
                        //ggd.Placement += y;
                        tmpPlacement +=y;
                    }
                    else
                    {
                        newInt = placementY.intValue();
                        dNewInt = new Double(newInt);
                        y = new String(dNewInt.toString());
                        //ggd.Placement += y;
                        tmpPlacement += y;
                    }
                }
                catch(Exception Ex)
                {
                    System.out.println("[ERROR] problems Flattening Graphics:"+Ex.toString());
                }
                
                ggd.Placement = tmpPlacement;
            }
        }
        
        if(seGrids == null)
        {
            seGrids = new Vector();
        }

        // the graphics in the screens in grids
        for(Enumeration en = seGrids.elements(); en.hasMoreElements();)
        {
            GridScreenDetails layoutScreenGrid = (GridScreenDetails) en.nextElement();
     
            if(layoutScreenGrid != null)
            {
                if(layoutScreenGrid.hasScreens)
                {
                    for(Enumeration enu = layoutScreenGrid.GetGridScreens().elements(); enu.hasMoreElements();)
                    {
                        String screen = (String) enu.nextElement();
                        
                        if(screen !=null)
                        {
                            for(Enumeration enm = layoutScreenGrid.GetScreenLayouts(screen); enm.hasMoreElements();)
                            {
                                DisplayLayoutDetails dld = (DisplayLayoutDetails) enm.nextElement();
                                
                                if(dld !=null)
                                {
                                    if(dld.hasGraphics)
                                    {
                                        for(Enumeration enume = dld.GetLayoutGraphics().elements(); enume.hasMoreElements();)
                                        {
                                            GraphicGuiDetails ggd = (GraphicGuiDetails) enume.nextElement();
                                            
                                            if(ggd!=null)
                                            {
                                                tmpPlacement = ggd.Placement;
                                                try
                                                {
                                                    fractionValueX = ggd.PlacementX()/divisor; // precision ?
                                                    placementX = new Double(ggd.PlacementX());
                                                        
                                                    if(fractionValueX>0)
                                                    {
                                                        newInt = placementX.intValue()+fractionValueX;
                                                        dNewInt = new Double(newInt);
                                                        x = new String(dNewInt.toString());
                                                        //ggd.Placement = x;
                                                        tmpPlacement = x;
                                                    }
                                                    else
                                                    {
                                                        newInt = placementX.intValue();
                                                        dNewInt = new Double(newInt);
                                                        x = new String(dNewInt.toString());
                                                        //ggd.Placement = x;
                                                        tmpPlacement = x;
                                                    }
                                                    
                                                    if(tmpPlacement.indexOf(":")<0)
                                                    {
                                                        tmpPlacement += ":";
                                                    }
                                                    
                                                    fractionValueY = ggd.PlacementY()/divisor; // precision ?
                                                    placementY = new Double(ggd.PlacementY());
                                                    
                                                    if(fractionValueY>0)
                                                    {
                                                            newInt = placementX.intValue()+fractionValueY;
                                                            dNewInt = new Double(newInt);
                                                            y = new String(dNewInt.toString());
                                                            //ggd.Placement += y;
                                                            tmpPlacement +=y;
                                                    }
                                                    else
                                                    {       newInt = placementX.intValue();
                                                            dNewInt = new Double(newInt);
                                                            y = new String(dNewInt.toString());
                                                            //ggd.Placement += y;
                                                            tmpPlacement +=y;
                                                    }
                                                }
                                                catch(Exception Ex)
                                                {
                                                    System.out.println("[ERROR] problems Flattening Graphics:"+Ex.toString());
                                                }
                                                
                                                ggd.Placement = tmpPlacement;
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
    
    public void FlattenGuiPlacements()
    {
        Double placementX, placementY;
        Double dNewInt;
        String x,y, tmpPlacement;
        boolean needsFlattening = false;
        double fractionValueX = 0;
        double fractionValueY = 0;
        double newInt = 0;
        int divisor = 10;
        
        if(seComponents ==null)
        {
            seComponents = new Vector();
        }
        
        // the seGraphics
        for(Enumeration e= seComponents.elements(); e.hasMoreElements();)
        {
            GraphicGuiDetails ggd = (GraphicGuiDetails) e.nextElement();
            
            if(ggd!=null)
            {
                tmpPlacement = ggd.Placement;
                
                try
                {
                    
                    fractionValueX = ggd.PlacementX()/divisor; // precision ?
                    placementX = new Double(ggd.PlacementX());
                    
                    if(fractionValueX>0)
                    {
                        newInt = placementX.intValue()+fractionValueX;
                        dNewInt = new Double(newInt);
                        x = new String(dNewInt.toString());
                        //ggd.Placement = x;
                        tmpPlacement = x;
                    }
                    else
                    {   
                        newInt = placementX.intValue();
                        dNewInt = new Double(newInt);
                        x = new String(dNewInt.toString());
                        //ggd.Placement = x;
                        tmpPlacement = x;
                    }
                
                    if(tmpPlacement.indexOf(":")<0)
                    {
                        //ggd.Placement += ":";
                        tmpPlacement += ":";
                    }
                
                    fractionValueY = ggd.PlacementY()/divisor; // precision ?
                    placementY = new Double(ggd.PlacementY());
                
                    if(fractionValueY>0)
                    {
                        newInt = placementY.intValue()+fractionValueY;
                        dNewInt = new Double(newInt);
                        y = new String(dNewInt.toString());
                        //ggd.Placement += y;
                        tmpPlacement += y;
                    }
                    else
                    {
                        newInt = placementY.intValue();
                        dNewInt = new Double(newInt);
                        y = new String(dNewInt.toString());
                        //ggd.Placement += y;
                        tmpPlacement +=y;
                    }
                }
                catch(Exception Ex)
                {
                    System.out.println("[ERROR] problems Flattening Graphics:"+Ex.toString());
                }
                
                ggd.Placement = tmpPlacement;
            }
        }

        if(seGrids == null)
        {
            seGrids = new Vector();
        }
        
        // the graphics in the screens in grids
        for(Enumeration en = seGrids.elements(); en.hasMoreElements();)
        {
            GridScreenDetails layoutScreenGrid = (GridScreenDetails) en.nextElement();
     
            if(layoutScreenGrid != null)
            {
                if(layoutScreenGrid.hasScreens)
                {
                    for(Enumeration enu = layoutScreenGrid.GetGridScreens().elements(); enu.hasMoreElements();)
                    {
                        String screen = (String) enu.nextElement();
                        
                        if(screen !=null)
                        {
                            for(Enumeration enm = layoutScreenGrid.GetScreenLayouts(screen); enm.hasMoreElements();)
                            {
                                DisplayLayoutDetails dld = (DisplayLayoutDetails) enm.nextElement();
                                
                                if(dld !=null)
                                {
                                    if(dld.hasComponents)
                                    {
                                        for(Enumeration enume = dld.GetLayoutComponents().elements(); enume.hasMoreElements();)
                                        {
                                            GraphicGuiDetails ggd = (GraphicGuiDetails) enume.nextElement();
                                            
                                            if(ggd!=null)
                                            {
                                                tmpPlacement = ggd.Placement;
                                                try
                                                {
                                                    fractionValueX = ggd.PlacementX()/divisor; // precision ?
                                                    placementX = new Double(ggd.PlacementX());
                                                        
                                                    if(fractionValueX>0)
                                                    {
                                                        newInt = placementX.intValue()+fractionValueX;
                                                        dNewInt = new Double(newInt);
                                                        x = new String(dNewInt.toString());
                                                        //ggd.Placement = x;
                                                        tmpPlacement = x;
                                                    }
                                                    else
                                                    {
                                                        newInt = placementX.intValue();
                                                        dNewInt = new Double(newInt);
                                                        x = new String(dNewInt.toString());
                                                        //ggd.Placement = x;
                                                        tmpPlacement = x;
                                                    }
                                                    
                                                    if(tmpPlacement.indexOf(":")<0)
                                                    {
                                                        tmpPlacement += ":";
                                                    }
                                                    
                                                    fractionValueY = ggd.PlacementY()/divisor; // precision ?
                                                    placementY = new Double(ggd.PlacementY());
                                                    
                                                    if(fractionValueY>0)
                                                    {
                                                            newInt = placementX.intValue()+fractionValueY;
                                                            dNewInt = new Double(newInt);
                                                            y = new String(dNewInt.toString());
                                                            //ggd.Placement += y;
                                                            tmpPlacement += y;
                                                    }
                                                    else
                                                    {       newInt = placementX.intValue();
                                                            dNewInt = new Double(newInt);
                                                            y = new String(dNewInt.toString());
                                                            //ggd.Placement += y;
                                                            tmpPlacement +=y;
                                                    }
                                                }
                                                catch(Exception Ex)
                                                {
                                                    System.out.println("[ERROR] problems Flattening Graphics:"+Ex.toString());
                                                }
                                                
                                                ggd.Placement = tmpPlacement;
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
    
    private static int[] rescaleArray(int[] ini, int x, int y, int x2, int y2)
    {
        int out[] = new int[x2*y2];

        for (int yy = 0; yy < y2; yy++)
        {
            int dy = yy * y / y2;
                        
            for (int xx = 0; xx < x2; xx++)
            {
                int dx = xx * x / x2;

                out[(x2 * yy) + xx] = ini[(x * dy) + dx];

            }
        }

        return out;
    }

    public static Bitmap resizeBitmap(Bitmap image, int width, int height)
    {       
        // Note from DCC:
        // an int being 4 bytes is large enough for Alpha/Red/Green/Blue in an 8-bit plane...
        // my brain was fried for a little while here because I am used to larger plane sizes for each
        // of the color channels....
        //
        //Need an array (for RGB, with the size of original image)
        //

        int rgb[] = new int[image.getWidth()*image.getHeight()];
        
        //Get the RGB array of image into "rgb"
        //
        image.getARGB(rgb, 0, image.getWidth(), 0, 0, image.getWidth(), image.getHeight());

        //Call to our function and obtain RGB2
        //
        int rgb2[] = rescaleArray(rgb, image.getWidth(), image.getHeight(), width, height);

        //Create an image with that RGB array
        //
        Bitmap temp2 = new Bitmap(width, height);
        temp2.setARGB(rgb2, 0, width, 0, 0, width, height);
        
        return temp2;

    }
} 

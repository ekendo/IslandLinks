/*
 * islandApplicationResource.java
 *
 * © <your company here>, 2003-2008
 * Confidential and proprietary.
 */

package com.ekendotech.homeIsland.resources;

import java.util.*;
import java.io.*;

import com.ekendotech.homeIsland.engine.*;

import net.rim.device.api.util.*;
import net.rim.device.api.xml.jaxp.DOMInternalRepresentation;

import org.kxml2.io.*;
import org.kxml2.kdom.*;

/**
 * This class handles th resources that an application has
 * GUI elements, icons, graphics, kernel mapping etc.
 */
public class islandApplicationResource extends islandResource
{
    // Gui Element Placement & Values
    public static final int GUI_ELEMENT =  0; 
    public static final int GUI_ELEMENT_ATTRIBUTE = 1;
    public static final int GUI_ELEMENT_LOCATION = 2;
    
    // Gui Images and values
    public static final int GUI_IMAGE_ELEMENT = 3;
    public static final int GUI_IMAGE_ELEMENT_ATTRIBUTE =4;
    public static final int GUI_IMAGE_ELEMENT_LOCATION = 5;
    
    // App Media and values
    public static final int MEDIA_AUDIO_ELEMENT = 23;
    public static final int MEDIA_VIDEO_ELEMENT = 24;
    
    // Gui Events
    public static final int GUI_ELEMENT_INITIATED = 6;
    public static final int GUI_ELEMENT_CLICKED = 7;
    public static final int GUI_ELEMENT_VALUE_CHANGED = 8;
    
    // Media Events
    public static final int MEDIA_ELEMENT_INITIATED = 25;
    public static final int MEDIA_ELEMENT_SELECTED = 27;
    public static final int MEDIA_ELEMENT_VALUE_CHANGED = 26;
    
    // Resource File Types
    public static final int XML_DATA_RESOURCE_LIST = 22;
    public static final int XML_MEDIA_RESOURCE_LIST = 21;
    public static final int XML_GUI_RESOURCE_LIST = 9;
    public static final int XML_OPERATION_LIST = 13;
    public static final int XML_ADDRESS_LIST = 14;
    public static final int XML_TRANSLATION_LIST = 15;
    public static final int BMP_IMAGE = 10;
    public static final int PNG_IMAGE = 11;
    public static final int AIFF_AUDIO = 19;
    public static final int WAV_AUDIO = 20;
    
    public static final String ICON_IMAGE = "Icon";
    public static final String BACK_IMAGE = "BackgroundImage";
    
    // Resource Memory Types
    public static final int STATIC_DATA_RESOURCE_LIST = 29;
    public static final int STATIC_MEDIA_RESOURCE_LIST = 28;
    public static final int STATIC_GUI_RESOURCE_LIST = 12;
    public static final int STATIC_OPERATION_LIST = 16;
    public static final int STATIC_ADDRESS_LIST = 17;
    public static final int STATIC_TRANSLATION_LIST = 18;
    
    private Hashtable mediaResourceList;
    private Hashtable dataResourceList;
    private Hashtable guiResourceList;
    private Hashtable operationList;
    private Hashtable addressList;
    private MultiMap translationList;
    
    private int type;
    private boolean debug;
    private String xmlMediaResourceList;
    private String xmlDataResourceList;
    private String xmlGuiResourceList;
    private String xmlTranslationList;
    private String xmlOperationList;
    private String xmlAddressList;
    
    public islandApplicationResource() 
    {   
        dataResourceList = new Hashtable();
        mediaResourceList = new Hashtable();
        guiResourceList = new Hashtable();
        operationList = new Hashtable();
        translationList = new MultiMap();
        addressList = new Hashtable();
    }

    public islandApplicationResource(boolean d) 
    {    
        dataResourceList = new Hashtable();
        mediaResourceList = new Hashtable();
        guiResourceList = new Hashtable();
        operationList = new Hashtable();
        translationList = new MultiMap();
        addressList = new Hashtable();
        
        this.debug = d;
    }
    
    public islandApplicationResource(int defaultType)
    {
        dataResourceList = new Hashtable();
        mediaResourceList = new Hashtable();
        guiResourceList = new Hashtable();
        operationList = new Hashtable();
        translationList = new MultiMap();
        addressList = new Hashtable();
        
        type = defaultType;
    }

    public islandApplicationResource(int defaultType, boolean d)
    {
        dataResourceList = new Hashtable();
        mediaResourceList = new Hashtable();
        guiResourceList = new Hashtable();
        operationList = new Hashtable();
        translationList = new MultiMap();
        addressList = new Hashtable();
        
        type = defaultType;
        
        this.debug = d;
    }
    
    public void addGuiResource(int resourceType, Object o)
    {
        guiResourceList.put(new Integer(resourceType), o);
        
    }
    
    public void addGuiResource(int resourceType, String s)
    {
        guiResourceList.put(new Integer(resourceType),s);
    }

    public void addMediaResource(int resourceType, Object o)
    {
        mediaResourceList.put(new Integer(resourceType), o);
        
    }
    
    public void addMediaResource(int resourceType, String s)
    {
        mediaResourceList.put(new Integer(resourceType),s);
    }
    
    public void addDataResource(int resourceType, Object o)
    {
        dataResourceList.put(new Integer(resourceType), o);
        
    }
    
    public void addDataResource(int resourceType, String s)
    {
        dataResourceList.put(new Integer(resourceType),s);
    }
    
    public void addAddress(int resourceType, Object o)
    {
        addressList.put(new Integer(resourceType), o);
        
    }
    
    public void addAddress(int resourceType, String s)
    {
        addressList.put(new Integer(resourceType),s);
    }
    
    public void addOperation(int resourceType, Object o)
    {
        operationList.put(new Integer(resourceType), o);
        
    }
    
    public void addOperation(int resourceType, String s)
    {
        operationList.put(new Integer(resourceType),s);
    }
    
    public void addTranslation(int resourceType, Object o)
    {
        translationList.add(new Integer(resourceType), o);
        
    }
    
    public void addTranslation(int resourceType, String s)
    {
        translationList.add(new Integer(resourceType),s);
    }

public boolean updateGuiResourceLocationWithValue(String value, Object v)
{
        boolean resourceUpdated = false;
        Object obKey = null;
        Object obKeyVal = null;
        Hashtable mediaTable;
        String hashKeyKey;

        if(this.debug)
            System.out.println("updateGuiResourceLocationWithValue, the value is: " + value);

        if(this.guiResourceList.contains(value))
        {

            //resourceList.put(value,v);
            for(Enumeration k = this.guiResourceList.keys(); k.hasMoreElements();)
            {

                obKey = k.nextElement();

                if((this.guiResourceList.get(obKey)) instanceof java.lang.String)
                {

                    if(value.equals((String) this.guiResourceList.get(obKey)))
                    {
                        guiResourceList.put(obKey,v);
                    }
                }
           }
        }
        else
        {
            try
            {

                boolean setValue = false;
                for(Enumeration k = this.guiResourceList.keys(); k.hasMoreElements();)
                {
                    obKey = k.nextElement();
    
                    if((this.guiResourceList.get(obKey)) instanceof java.util.Hashtable)
                    {

                        Hashtable hash = (Hashtable) this.guiResourceList.get(obKey);

                        for(Enumeration l = hash.keys(); l.hasMoreElements();)
                        {
                            obKeyVal = (Object) l.nextElement();
                            
                            if(obKeyVal instanceof java.lang.String)
                            {
                                String hashKey = (String) obKeyVal;
    
                                if(hash.get(hashKey) instanceof java.lang.String)
                                {
    
                                    if(value.equals((String) hash.get(hashKey)))
                                    {
                                        hash.put(hashKey,v);
                                        setValue = true;
                                    }
                                }
                                
                                if(hash.get(hashKey) instanceof java.util.Hashtable)
                                {
                                    mediaTable = (Hashtable) hash.get(hashKey);
                                    for(Enumeration m= mediaTable.keys(); m.hasMoreElements();)
                                    {
                                        try
                                        {
                                            hashKeyKey = (String) m.nextElement();
                                            if(mediaTable.get(hashKeyKey) instanceof java.lang.String)
                                            {
                                                String hashKeyValue = (String) mediaTable.get(hashKeyKey);
                                                
                                                if(hashKeyValue.equals(value))
                                                {
                                                    mediaTable.put(hashKeyKey,v);
                                                    if(this.debug)
                                                    {
                                                        System.out.println("[INFO] Placed Media Data in: " + hashKeyKey);
                                                    }
                                                    setValue = true;
                                                }
                                            }                                            
                                        }
                                        catch(Exception e)
                                        {
                                            if(this.debug)
                                            {
                                                System.out.println("[ERROR] problems updating hashtable url key with actual resource value"+e.toString());
                                            }
                                        }
                                    }
                                }
                                
                                if(setValue)
                                {
                                    //break;
                                }
                            }
                        }
                        
                        guiResourceList.put(obKey,hash);
                    }

                    if(setValue)
                    {
                        break;
                    }
                }
            }
            catch(Exception e)
            {
                if(this.debug)
                {
                    System.out.println("[ERROR] problems updating resource List:"+e.toString());
                }
            }
        }
        
        return resourceUpdated;
    }

    public boolean updateDataResourceLocationWithValue(String value, Object v)
    {
        boolean resourceUpdated = false;
        Object obKey = null;
        
        if(this.dataResourceList.contains(value))
        {
            //resourceList.put(value,v);
            for(Enumeration k = this.dataResourceList.keys(); k.hasMoreElements();)
            {
                obKey = k.nextElement();
                
                if((this.dataResourceList.get(obKey)) instanceof java.lang.String)
                {
                    if(value.equals((String) this.dataResourceList.get(obKey)))
                    {
                        dataResourceList.put(obKey,v);
                    }
                }
           }
        }
        else
        {
            try
            {
                for(Enumeration k = this.guiResourceList.keys(); k.hasMoreElements();)
                {
                    obKey = k.nextElement();
                    
                    if((this.dataResourceList.get(obKey)) instanceof java.util.Hashtable)
                    {
                        Hashtable hash = (Hashtable) this.dataResourceList.get(obKey);
                        
                        for(Enumeration l = hash.keys(); l.hasMoreElements();)
                        {
                            String hashKey = (String) l.nextElement();
                            
                            if(hash.get(hashKey) instanceof java.lang.String)
                            {
                                if(value.equals((String) hash.get(hashKey)))
                                {
                                    hash.put(hashKey,v);
                                }
                            }
                        }
                        
                        dataResourceList.put(obKey,hash);
                    }
                }
            }
            catch(Exception e)
            {
                if(this.debug)
                {
                    System.out.println("[ERROR] problems updating resource List:"+e.toString());
                }
            }
        }
        
        return resourceUpdated;
    }

    public boolean updateMediaResourceLocationWithValue(String value, Object v)
    {
        boolean resourceUpdated = false;
        Object obKey = null;
        
        if(this.mediaResourceList.contains(value))
        {
            //resourceList.put(value,v);
            for(Enumeration k = this.mediaResourceList.keys(); k.hasMoreElements();)
            {
                obKey = k.nextElement();
                
                if((this.mediaResourceList.get(obKey)) instanceof java.lang.String)
                {
                    if(value.equals((String) this.mediaResourceList.get(obKey)))
                    {
                        mediaResourceList.put(obKey,v);
                    }
                }
           }
        }
        else
        {
            try
            {
                for(Enumeration k = this.mediaResourceList.keys(); k.hasMoreElements();)
                {
                    obKey = k.nextElement();
                    
                    if((this.mediaResourceList.get(obKey)) instanceof java.util.Hashtable)
                    {
                        Hashtable hash = (Hashtable) this.mediaResourceList.get(obKey);
                        
                        for(Enumeration l = hash.keys(); l.hasMoreElements();)
                        {
                            String hashKey = (String) l.nextElement();
                            
                            if(hash.get(hashKey) instanceof java.lang.String)
                            {
                                if(value.equals((String) hash.get(hashKey)))
                                {
                                    hash.put(hashKey,v);
                                }
                            }
                        }
                        
                        mediaResourceList.put(obKey,hash);
                    }
                }
            }
            catch(Exception e)
            {
                if(this.debug)
                {
                    System.out.println("[ERROR] problems updating resource List:"+e.toString());
                }
            }
        }
        
        return resourceUpdated;
    }


    public boolean updateGuiResourceLocationWithValue(String value, String v)
    {
        boolean resourceUpdated = false;
        Object obKey = null;
        
        if(this.guiResourceList.contains(value))
        {
            //resourceList.put(value,v);
            for(Enumeration k = this.guiResourceList.keys(); k.hasMoreElements();)
            {
                obKey = k.nextElement();
                
                if((this.guiResourceList.get(obKey)) instanceof java.lang.String)
                {
                    if(value.equals((String) this.guiResourceList.get(obKey)))
                    {
                        guiResourceList.put(obKey,v);
                    }
                }
            }
        }
        
        return resourceUpdated;
    }
    
    public boolean updateDataResourceLocationWithValue(String value, String v)
    {
        boolean resourceUpdated = false;
        Object obKey = null;
        
        if(this.dataResourceList.contains(value))
        {
            //resourceList.put(value,v);
            for(Enumeration k = this.dataResourceList.keys(); k.hasMoreElements();)
            {
                obKey = k.nextElement();
                
                if((this.dataResourceList.get(obKey)) instanceof java.lang.String)
                {
                    if(value.equals((String) this.dataResourceList.get(obKey)))
                    {
                        dataResourceList.put(obKey,v);
                    }
                }
            }
        }
        
        return resourceUpdated;
    }

    public boolean updateMediaResourceLocationWithValue(String value, String v)
    {
        boolean resourceUpdated = false;
        Object obKey = null;
        
        if(this.mediaResourceList.contains(value))
        {
            //resourceList.put(value,v);
            for(Enumeration k = this.mediaResourceList.keys(); k.hasMoreElements();)
            {
                obKey = k.nextElement();
                
                if((this.mediaResourceList.get(obKey)) instanceof java.lang.String)
                {
                    if(value.equals((String) this.mediaResourceList.get(obKey)))
                    {
                        mediaResourceList.put(obKey,v);
                    }
                }
            }
        }
        
        return resourceUpdated;
    }
    /*
    public boolean UnpackXmlGuiResourceList() 
    {
        boolean unpacked = false;
        KXmlParser parser = null;
        String xmlContent = (String)this.guiResourceList.get(new Integer(islandApplicationResource.XML_GUI_RESOURCE_LIST));
        Hashtable values = null;
        String name = "";
        String value = "";
        
        
        try
        {
            ByteArrayInputStream is = new ByteArrayInputStream(xmlContent.getBytes());            
            parser = new KXmlParser();
            parser.setInput(is,null);
            //Document doc = docb.parse(is);
            org.kxml2.kdom.Document doc = new org.kxml2.kdom.Document();
            doc.parse(parser);
            parser = null;
            
            
            //NodeList nl = doc.getElementsByTagName("Resources");
            Element el = doc.getRootElement();
            
            System.out.println("[INFO] node childCount:"+el.getChildCount());
            
            for(int i=0; i<el.getChildCount(); i++)
            {
                if(el.getChild(i) instanceof Element)
                {
                    Element ce = (Element) el.getChild(i);
                    
                    if(this.debug)
                    {
                        System.out.println("Name:"+ce.getName());
                    }
                
                    if(ce.getName().length()>0)
                    {
                        name = ce.getName();
                    }
                    
                    if(ce.getChildCount()>0)
                    {
                        for(int j=0; j<ce.getChildCount();j++)
                        {
                            if(ce.getChild(j) instanceof java.lang.String)
                            {
                                if(this.debug)
                                {
                                    System.out.println("Element Value:"+ce.getChild(j));
                                }
                                
                                if(((String)ce.getChild(j)).length()>0)
                                {
                                    value = (String)ce.getChild(j);
                                }
                            }
                            
                            if(ce.getChild(j) instanceof Element)
                            {
                                Element cce = (Element) ce.getChild(j);
                               
                                if(this.debug)
                                {
                                    System.out.println("Next Element Name:"+cce.getName());
                                }
                                
                                // check attributes
                                if(cce.getAttributeCount()>0)
                                {
                                    for(int k=0; i<cce.getAttributeCount(); k++)
                                    {
                                        if(values == null)
                                        {
                                            values = new Hashtable();
                                        }
                                        
                                        if(cce.getChild(k) instanceof java.lang.String)
                                        {
                                            value =  (String) cce.getChild(k);
                                            values.put(cce.getAttributeValue(k),value);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                if(values == null) // no attributes
                {
                    if((name!=null)&&(value!=null))
                    {
                        if((name.length()>0)&&(value.length()>0))
                        {
                            this.guiResourceList.put(name,value);
                        }
                    }
                }
                else
                {
                    if(this.debug)
                    {
                        System.out.println("Element Name:"+name);
                    }
                    
                    this.guiResourceList.put(name,values);
                }    
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems unpacking xml Resource List:"+ex.toString());
                ex.printStackTrace();
            }
        }
        
        if(guiResourceList.size()>0)
        {
            unpacked = true;
        }
        
        return unpacked;
    }
    */
    
    public boolean UnpackXmlMediaResourceList() 
    {
        boolean unpacked = false;
        KXmlParser parser = null;
        String xmlContent = (String)this.dataResourceList.get(new Integer(islandApplicationResource.XML_MEDIA_RESOURCE_LIST));
        Hashtable values = null;
        String name = "";
        String value = "";
        
        
        try
        {
            /*
            DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
            DocumentBuilder docb = dbf.newDocumentBuilder();
            */
            ByteArrayInputStream is = new ByteArrayInputStream(xmlContent.getBytes());            
            parser = new KXmlParser();
            parser.setInput(is,null);
            //Document doc = docb.parse(is);
            org.kxml2.kdom.Document doc = new org.kxml2.kdom.Document();
            doc.parse(parser);
            parser = null;
            
            
            //NodeList nl = doc.getElementsByTagName("Resources");
            Element el = doc.getRootElement();
            
            System.out.println("[INFO] node childCount:"+el.getChildCount());
            
            for(int i=0; i<el.getChildCount(); i++)
            {
                if(el.getChild(i) instanceof Element)
                {
                    Element ce = (Element) el.getChild(i);
                    
                    if(this.debug)
                    {
                        System.out.println("Name:"+ce.getName());
                    }
                
                    if(ce.getName().length()>0)
                    {
                        name = ce.getName();
                    }
                    
                    if(ce.getChildCount()>0)
                    {
                        for(int j=0; j<ce.getChildCount();j++)
                        {
                            if(ce.getChild(j) instanceof java.lang.String)
                            {
                                if(this.debug)
                                {
                                    System.out.println("Element Value:"+ce.getChild(j));
                                }
                                
                                if(((String)ce.getChild(j)).length()>0)
                                {
                                    value = (String)ce.getChild(j);
                                }
                            }
                            
                            if(ce.getChild(j) instanceof Element)
                            {
                                Element cce = (Element) ce.getChild(j);
                               
                                if(this.debug)
                                {
                                    System.out.println("Next Element Name:"+cce.getName());
                                }
                                
                                // check attributes
                                if(cce.getAttributeCount()>0)
                                {
                                    if(values == null)
                                    {
                                        values = new Hashtable();
                                    }
                                    
                                    if(cce.getChild(0) instanceof java.lang.String)
                                    {
                                        value =  (String) cce.getChild(0);
                                        values.put(cce.getAttributeValue(0),value);
                                    }
                                }
                            }
                        }
                    }
                }
                
                if(values == null)
                {
                    if((name!=null)&&(value!=null))
                    {
                        if((name.length()>0)&&(value.length()>0))
                        {
                            this.mediaResourceList.put(name,value);
                        }
                    }
                }
                else
                {
                    this.mediaResourceList.put("BmpImages",values);
                }    
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems unpacking xml Resource List:"+ex.toString());
                ex.printStackTrace();
            }
        }
        
        if(mediaResourceList.size()>0)
        {
            unpacked = true;
        }
        
        return unpacked;
    }
        
    public boolean UnpackXmlDataResourceList() 
    {
        boolean unpacked = false;
        KXmlParser parser = null;
        String xmlContent = (String)this.dataResourceList.get(new Integer(islandApplicationResource.XML_DATA_RESOURCE_LIST));
        Hashtable values = null;
        String name = "";
        String value = "";
        
        
        try
        {
            /*
            DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
            DocumentBuilder docb = dbf.newDocumentBuilder();
            */
            ByteArrayInputStream is = new ByteArrayInputStream(xmlContent.getBytes());            
            parser = new KXmlParser();
            parser.setInput(is,null);
            //Document doc = docb.parse(is);
            org.kxml2.kdom.Document doc = new org.kxml2.kdom.Document();
            doc.parse(parser);
            parser = null;
            
            
            //NodeList nl = doc.getElementsByTagName("Resources");
            Element el = doc.getRootElement();
            
            System.out.println("[INFO] node childCount:"+el.getChildCount());
            
            for(int i=0; i<el.getChildCount(); i++)
            {
                if(el.getChild(i) instanceof Element)
                {
                    Element ce = (Element) el.getChild(i);
                    
                    if(this.debug)
                    {
                        System.out.println("Name:"+ce.getName());
                    }
                
                    if(ce.getName().length()>0)
                    {
                        name = ce.getName();
                    }
                    
                    if(ce.getChildCount()>0)
                    {
                        for(int j=0; j<ce.getChildCount();j++)
                        {
                            if(ce.getChild(j) instanceof java.lang.String)
                            {
                                if(this.debug)
                                {
                                    System.out.println("Element Value:"+ce.getChild(j));
                                }
                                
                                if(((String)ce.getChild(j)).length()>0)
                                {
                                    value = (String)ce.getChild(j);
                                }
                            }
                            
                            if(ce.getChild(j) instanceof Element)
                            {
                                Element cce = (Element) ce.getChild(j);
                               
                                if(this.debug)
                                {
                                    System.out.println("Next Element Name:"+cce.getName());
                                }
                                
                                // check attributes
                                if(cce.getAttributeCount()>0)
                                {
                                    if(values == null)
                                    {
                                        values = new Hashtable();
                                    }
                                    
                                    if(cce.getChild(0) instanceof java.lang.String)
                                    {
                                        value =  (String) cce.getChild(0);
                                        values.put(cce.getAttributeValue(0),value);
                                    }
                                }
                            }
                        }
                    }
                }
                
                if(values == null)
                {
                    if((name!=null)&&(value!=null))
                    {
                        if((name.length()>0)&&(value.length()>0))
                        {
                            this.dataResourceList.put(name,value);
                        }
                    }
                }
                else
                {
                    this.dataResourceList.put("BmpImages",values);
                }    
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems unpacking xml Resource List:"+ex.toString());
                ex.printStackTrace();
            }
        }
        
        if(dataResourceList.size()>0)
        {
            unpacked = true;
        }
        
        return unpacked;
    }    

    public boolean UnpackXmlOperationList() 
    {
        boolean unpacked = false;
        KXmlParser parser = null;
        String xmlContent = (String)this.operationList.get(new Integer(islandApplicationResource.XML_OPERATION_LIST));
        Hashtable values = null;
        String name = "";
        String address = "";
        String operation = "";
        String procType = "";
        String procArchVersion = "";
        String procUsesMMU = "";
        String value = "";
        
        
        try
        {
            ByteArrayInputStream is = new ByteArrayInputStream(xmlContent.getBytes());            
            parser = new KXmlParser();
            parser.setInput(is,null);
            org.kxml2.kdom.Document doc = new org.kxml2.kdom.Document();
            doc.parse(parser);
            parser = null;
            
            
            Element el = doc.getRootElement();
            
            if(this.debug)
            {
                System.out.println("[INFO] node childCount:"+el.getChildCount());
            }
            
            if(el.getAttributeCount()>0)
            {
                if(this.debug)
                {
                    System.out.println("[INFO] Root Element Attribute is:"+el.getAttributeValue(0));
                }
                
                procType = el.getAttributeValue(0);
                procArchVersion = el.getAttributeValue(1);
                procUsesMMU = el.getAttributeValue(2);
            }
            
            for(int i=0; i<el.getChildCount(); i++)
            {
                if(el.getChild(i) instanceof Element)
                {
                    Element ce = (Element) el.getChild(i);
                    
                    if(this.debug)
                    {
                        System.out.println("Name:"+ce.getName());
                    }
                
                    if(ce.getName().length()>0)
                    {
                        name = ce.getName();
                    }
                    
                    if(ce.getAttributeCount()>0)
                    {
                        if(this.debug)
                        {
                            System.out.println("Attribute Value:"+ce.getAttributeValue(0));
                        }
                                    
                        if(values == null)
                        {
                            values = new Hashtable();
                        }
                                
                        if(((String)ce.getAttributeValue(0)).length()>0)
                        {
                            address = (String)ce.getAttributeValue(0);
                        }
                    }
                    
                    if(ce.getChildCount()>0)
                    {
                        for(int j=0; j<ce.getChildCount();j++)
                        {
                            if(ce.getChild(j) instanceof Element)
                            {
                                Element cce = (Element) ce.getChild(j);
                               
                                if(this.debug)
                                {
                                    System.out.println("Next Element Name:"+cce.getName());
                                }
                                
                                operation = cce.getAttributeValue(0);
                                
                                if(this.debug)
                                {
                                    System.out.println("Next Attribute Name:"+operation);
                                }
                                
                                if(cce.getChildCount()>0)
                                {
                                    for(int k=0; k<cce.getChildCount();k++)
                                    {
                                        if(cce.getChild(k) instanceof java.lang.String)
                                        {
                                            if(this.debug)
                                            {
                                                System.out.println("Element Value:"+cce.getChild(k));
                                            }
                                            
                                            if(((String)cce.getChild(k)).length()>0)
                                            {
                                                value = (String)cce.getChild(k);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                if(values != null)
                {
                    values.put(address,operation+":"+value);
                    
                    if(procUsesMMU.equals("1"))
                    {
                        this.operationList.put(procType+"v"+procArchVersion+":MMU",values);
                    }
                    else
                    {
                        this.operationList.put(procType+"v"+procArchVersion+":NoMMU",values);
                    }
                }    
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems unpacking xml Resource List:"+ex.toString());
                ex.printStackTrace();
            }
        }
        
        if(this.operationList.size()>0)
        {
            unpacked = true;
        }
        
        return unpacked;
    }
    
    public boolean UnpackXmlTranslationList() 
    {
        boolean unpacked = false;
        KXmlParser parser = null;
        Enumeration xmlContentEnumer = this.translationList.elements(new Integer(islandApplicationResource.XML_TRANSLATION_LIST));
        String xmlContent = "";
        Hashtable values = null;
        String name = "";
        String value = "";
        String type = "";
        
        
        try
        {
            
            
            for(Enumeration e = xmlContentEnumer; e.hasMoreElements();)
            {
                xmlContent = (String) e.nextElement();
            }
            
            ByteArrayInputStream is = new ByteArrayInputStream(xmlContent.getBytes());            
            parser = new KXmlParser();
            parser.setInput(is,null);
            org.kxml2.kdom.Document doc = new org.kxml2.kdom.Document();
            doc.parse(parser);
            parser = null;
            Element el = doc.getRootElement();
            
            System.out.println("[INFO] There are "+el.getChildCount() + " main groups in the Translation Mapping XML file");
            
            for(int i=0; i<el.getChildCount(); i++)
            {
                name = "";
                if(el.getChild(i) instanceof Element)
                {
                    Element ce = (Element) el.getChild(i);
                    
                    //There are two children to the root - gridSettings and guiMappings - we process them separately here:
                    if(ce.getName().length()>0)
                    {
                        name = ce.getName();
                        if(this.debug)
                        {
                            System.out.println("Processing Translation Group: "+ce.getName());
                        }
                        if(name.equals("gridSettings"))
                        {
                            for(int j=0; j<ce.getChildCount();j++)
                            {
                                if(ce.getChild(j) instanceof Element)
                                {
                                    Element cce = (Element) ce.getChild(j);
                                    
                                    //First we need to grab the import attribute to see if we are importing this entry:
                                    value = cce.getAttributeValue("","import");
                                    type = cce.getAttributeValue("","type");
                                    
                                    if(value.equals("yes"))
                                    {
                                        //We are importing this value
                                        if(this.debug)
                                        {
                                            System.out.println("Importing gridSetting: "+type);
                                        }
                                        
                                        if(type.equals("APP_GRID"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRID), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRID");
                                            }
                                        }
                                        else if(type.equals("APP_GRID_ORIGIN"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRID_ORIGIN), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRID_ORIGIN");
                                            }
                                        }
                                        else if(type.equals("APP_GRID_MAX"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRID_MAX), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRID_MAX");
                                            }
                                        }
                                        else if(type.equals("APP_GRID_COLOR"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRID_COLOR), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRID_COLOR");
                                            }
                                        }
                                        else if(type.equals("APP_GRID_EVENT"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRID_EVENT), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRID_EVENT");
                                            }
                                        }
                                        else
                                        {
                                            if(this.debug)
                                            {
                                                System.out.println("[ERROR] DID NOT FIND TRANSLATION MAPPING MATCH");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        else if(name.equals("guiMappings"))
                        {
                            for(int j=0; j<ce.getChildCount();j++)
                            {
                                if(ce.getChild(j) instanceof Element)
                                {
                                    Element cce = (Element) ce.getChild(j);
                                    
                                    //First we need to grab the import attribute to see if we are importing this entry:
                                    value = cce.getAttributeValue("","import");
                                    type = cce.getAttributeValue("","type");
                                    
                                    if(value.equals("yes"))
                                    {
                                        //We are importing this value
                                        if(this.debug)
                                        {
                                            System.out.println("Importing guiMapping: "+type);
                                        }
                                        
                                        if(type.equals("APP_GRID_LAYOUT"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRID_LAYOUT), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRID_LAYOUT");
                                            }
                                        }
                                        else if(type.equals("APP_GRID_LAYOUT_ORIGIN"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRID_LAYOUT_ORIGIN), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRID_LAYOUT_ORIGIN");
                                            }
                                        }
                                        else if(type.equals("APP_GRID_LAYOUT_COLOR"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRID_LAYOUT_COLOR), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRID_LAYOUT_COLOR");
                                            }
                                        }
                                        else if(type.equals("APP_GUI_PLACEMENT"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GUI_PLACEMENT), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GUI_PLACEMENT");
                                            }
                                        }
                                        else if(type.equals("APP_GUI_EVENT"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GUI_EVENT), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GUI_EVENT");
                                            }
                                        }
                                        else if(type.equals("APP_GUI_COLOR"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GUI_COLOR), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GUI_COLOR");
                                            }
                                        }
                                        else if(type.equals("APP_GRAPHIC_PLACEMENT"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRAPHIC_PLACEMENT), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRAPHIC_PLACEMENT");
                                            }
                                        }
                                        else if(type.equals("APP_GRAPHIC_WIDTH"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRAPHIC_WIDTH), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRAPHIC_WIDTH");
                                            }
                                        }
                                        else if(type.equals("APP_GRAPHIC_HEIGHT"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRAPHIC_HEIGHT), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRAPHIC_HEIGHT");
                                            }
                                        }
                                        else if(type.equals("APP_GRAPHIC_EVENT"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRAPHIC_EVENT), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRAPHIC_EVENT");
                                            }
                                        }
                                        else if(type.equals("APP_GRAPHIC_COLOR"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.APP_GRAPHIC_COLOR), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to APP_GRAPHIC_COLOR");
                                            }
                                        }
                                        else if(type.equals("KERNEL_CPU"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.KERNEL_CPU), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to KERNEL_CPU");
                                            }
                                        }
                                        else if(type.equals("PROCESSOR_STACK_EVENT"))
                                        {
                                            //applicationOperationMapping.add(new Integer(islandSoftwareEngine.PROCESSOR_STACK_EVENT), cce.getChild(0));
                                            this.operationList.put(new Integer(islandProcessorEngine.PROCESSOR_STACK_EVENT), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to PROCESSOR_STACK_EVENT");
                                            }
                                        }
                                        else if(type.equals("KERNEL_MEMORY"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.KERNEL_MEMORY), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to KERNEL_MEMORY");
                                            }
                                        }
                                        else if(type.equals("KERNEL_IO"))
                                        {
                                            this.translationList.add(new Integer(islandSoftwareEngine.KERNEL_IO), cce.getChild(0));
                                            if(this.debug)
                                            {
                                                System.out.println("Added " + cce.getChild(0) + " to KERNEL_IO");
                                            }
                                        }
                                        else
                                        {
                                            if(this.debug)
                                            {
                                                System.out.println("[ERROR] DID NOT FIND TRANSLATION MAPPING MATCH");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            if(this.debug)
                            {
                                System.out.println("[ERROR] UNKNOWN GROUP");
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
                System.out.println("[ERROR]PROBLEMS UNPACKING TRANSLATION XML FILE:"+ex.toString());
                ex.printStackTrace();
            }
        }
        
        if(this.translationList.size()>0)
        {
            unpacked = true;
        }
       
        return unpacked;
    }

    public boolean UnpackXmlAddressList() 
    {
        boolean unpacked = false;
        KXmlParser parser = null;
        String xmlContent = (String)this.addressList.get(new Integer(islandApplicationResource.XML_ADDRESS_LIST));
        Hashtable values = null;
        String name = "";
        String label = "";
        String addressLabel = "";
        String value = "";
        
        
        try
        {
            ByteArrayInputStream is = new ByteArrayInputStream(xmlContent.getBytes());            
            parser = new KXmlParser();
            parser.setInput(is,null);
            //Document doc = docb.parse(is);
            org.kxml2.kdom.Document doc = new org.kxml2.kdom.Document();
            doc.parse(parser);
            parser = null;
            
            
            //NodeList nl = doc.getElementsByTagName("Resources");
            Element el = doc.getRootElement();
            
            if(this.debug)
            {
                System.out.println("[INFO] node childCount:"+el.getChildCount());
            }
            
            if(el.getAttributeCount()>0)
            {
                if(this.debug)
                {
                    System.out.println("[INFO] Root Element Attribute is:"+el.getAttributeValue(0));
                }
                
                addressLabel = el.getAttributeValue(0);
            }
            
            for(int i=0; i<el.getChildCount(); i++)
            {
                if(el.getChild(i) instanceof Element)
                {
                    Element ce = (Element) el.getChild(i);
                    
                    if(this.debug)
                    {
                        System.out.println("Name:"+ce.getName());
                    }
                
                    if(ce.getName().length()>0)
                    {
                        name = ce.getName();
                    }
                    
                    if(ce.getAttributeCount()>0)
                    {
                        if(this.debug)
                        {
                            System.out.println("Attribute Value:"+ce.getAttributeValue(0));
                        }
                                    
                        if(values == null)
                        {
                            values = new Hashtable();
                        }
                                
                        if(((String)ce.getAttributeValue(0)).length()>0)
                        {
                            label = (String)ce.getAttributeValue(0);
                        }
                    }
                    
                    if(ce.getChildCount()>0)
                    {
                        for(int j=0; j<ce.getChildCount();j++)
                        {
                            if(ce.getChild(j) instanceof java.lang.String)
                            {
                                if(this.debug)
                                {
                                    System.out.println("Element Value:"+ce.getChild(j));
                                }
                                
                                if(((String)ce.getChild(j)).length()>0)
                                {
                                    value = (String)ce.getChild(j);
                                }
                            }
                            
                            if(ce.getChild(j) instanceof Element)
                            {
                                Element cce = (Element) ce.getChild(j);
                               
                                if(this.debug)
                                {
                                    System.out.println("Next Element Name:"+cce.getName());
                                }
                            }
                        }
                    }
                }
                
                if(values == null)
                {
                    if((label!=null)&&(value!=null))
                    {
                        if((label.length()>0)&&(value.length()>0))
                        {
                            this.addressList.put(label,value);
                        }
                    }
                }
                else
                {
                    values.put(label,value);
                    
                    this.addressList.put(addressLabel,values);
                }    
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems unpacking xml Address List:"+ex.toString());
                ex.printStackTrace();
            }
        }
        
        if(this.addressList.size()>0)
        {
            unpacked = true;
        }
        
        return unpacked;
    }

    
    public boolean UnpackStaticGuiResourceList(MultiMap resources, MultiMap translations)
    {
        boolean unpacked = false;
        Hashtable values = null;
        String name = "";
        String value = "";
        Object element = null;
        
        try
        {
            for(Enumeration e = resources.elements(); e.hasMoreElements();)
            {
                element = e.nextElement();
                // get elements
                
                if(element instanceof Hashtable)
                {
                    if(this.debug)
                    {
                        for(Enumeration en = ((Hashtable)element).keys(); en.hasMoreElements();)
                        {
                            System.out.println("[INFO] sub-hash key:"+en.nextElement());
                        }
                    }
                }
                
                if(element instanceof String)
                {
                    String k = (String)element;
                    
                    // should have a colon and a location
                    if((k.indexOf("_")>=0)&&(k.indexOf(":")>=0)&&(k.indexOf("Location")>=0))
                    {
                        name = k.substring(0,k.indexOf("_"));
                        String loc = k.substring(k.indexOf(":")+1); 
                        
                        // get all the network elements
                        for(Enumeration f = translations.elements(new Integer(islandSoftwareEngine.KERNEL_NETWORK)); f.hasMoreElements();)
                        {
                            String l = (String) f.nextElement();
                            
                            if(l.indexOf(loc)>=0)
                            {
                                value = l.substring(l.indexOf("_"));
                        
                                if((name!=null)&&(value!=null))
                                {
                                    if((name.length()>0)&&(value.length()>0))
                                    {
                                        if(value.indexOf("HTTP")>=0)
                                        {
                                            if(loc.indexOf("[")>0)
                                            {
                                                value = "http://" + 
                                                    loc.substring(0,loc.indexOf("[")) + 
                                                        value.substring(value.indexOf(":")+1);
                                            }
                                        }
                                        
                                        if(values==null)
                                        {
                                            values = new Hashtable();
                                        }
                                        //this.guiResourceList.put(name,value);
                                        values.put(name,value);
                                        
                                        if(value.indexOf(".png")>=0)
                                        {
                                            this.guiResourceList.put("PngImages", values);
                                        }
                                        
                                        if(value.indexOf(".bmp")>=0)
                                        {
                                            this.guiResourceList.put("BmpImages", values);
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
                System.out.println("[ERROR] problems unpacking xml Resource List:"+ex.toString());
                ex.printStackTrace();
            }
        }
        if(guiResourceList.size()>0)
        {
            unpacked = true;
        }
        
        return unpacked;
    }
    
    /* new method */
    public boolean UnpackXmlGuiResourceList() 
    {
        boolean unpacked = false;
        KXmlParser parser = null;
        String xmlContent = (String)this.guiResourceList.get(new Integer(islandApplicationResource.XML_GUI_RESOURCE_LIST));
        Hashtable values = null;
        String name = "";
        String value = "";
        Hashtable pngTable = new Hashtable();
        Hashtable imageTable = new Hashtable();
        Hashtable bmpTable = new Hashtable();
        Hashtable audioTable = new Hashtable();
        

        try
        {
            //Remove any unwanted elements...
            //this.guiResourceList.clear();
            
            //This part grabs and loads the XML file
            ByteArrayInputStream is = new ByteArrayInputStream(xmlContent.getBytes());            
            parser = new KXmlParser();
            parser.setInput(is,null);

            //Document doc = docb.parse(is);
            org.kxml2.kdom.Document doc = new org.kxml2.kdom.Document();
            doc.parse(parser);
            parser = null;

            //NodeList nl = doc.getElementsByTagName("Resources");
            //Grab the Resources Element

            Element el = doc.getRootElement();

            if(this.debug) 
            {
                System.out.println("[INFO] Main Groups in XML File: "+el.getChildCount());
            }
                
            //Iterate through Icon, Images and Audio elements here
            String globalHashName = "";

            for(int i=0; i<el.getChildCount(); i++)
            {
                globalHashName = "Icon";
                values = null;
                value = "";

                if(el.getChild(i) instanceof Element)
                {

                    Element ce = (Element) el.getChild(i);

                    if(this.debug)
                    {
                        System.out.println("Name:"+ce.getName());
                    }

                    if(ce.getName().length()>0)
                    {
                        name = ce.getName();
                    }

                    if(ce.getChildCount()>0)
                    {
                        //This For loop iterates through any child elements such as Image or Format
                        for(int j=0; j<ce.getChildCount();j++)
                        {
                            //If the "child" is just a URL, this check will capture it...
                            if(ce.getChild(j) instanceof java.lang.String)
                            {
                                //This only works for the Icon element...
                                if(((String)ce.getChild(j)).length()>0)
                                {
                                    value = (String)ce.getChild(j);
                                    
                                    //Since this only works for the Icon as it is currently constructed, we need to simply add it
                                    if(value.length()>0 && value != "" && value.indexOf("\n") == -1)
                                    {
                                        if(this.debug)
                                        {
                                            System.out.println("Adding " + globalHashName + " and value: " + value + " to the guiResourceList.");
                                        }
                                        this.guiResourceList.put(globalHashName,value);
                                        value = "";
                                    }
                                }
                            }
                            else if(ce.getChild(j) instanceof Element)
                            {
                                //This call will pull up the children of an image or audio file
                                Element cce = (Element) ce.getChild(j);

                                if(this.debug)
                                {
                                    System.out.println("Next Element Name:"+cce.getName());
                                }

                                //We create a new hashtable for each iteration
                                values = new Hashtable();

                                //First we need to grab our main Image Location
                                values.put("URL",cce.getChild(0).toString());

                                if(this.debug)
                                {
                                    System.out.println("Added URL:" + cce.getChild(0).toString() + " to values Hashtable.");
                                }

                                // check attributes
                                if(cce.getAttributeCount()>0)
                                {

                                    for(int q=0; q < cce.getAttributeCount(); q++)
                                    {
                                        if(this.debug)
                                        {

                                            System.out.println("Attribute Name:"+cce.getAttributeName(q).toString());
                                            System.out.println("Attribute Value:"+cce.getAttributeValue(q).toString());
                                        }

                                        if(cce.getAttributeValue(q) != null && cce.getAttributeValue(q).length() > 0 && cce.getAttributeValue(q).toString() != "" && cce.getAttributeValue(q).toString() != "null")
                                        {   
                                            //The original program was built without multiple atrributes in mind
                                            //To overcome this, for now, we add in the Name as a lookup to the URL
                                            if(cce.getAttributeName(q).equals("Name"))
                                            {
                                                name = cce.getAttributeValue(q).toString();
                                                values.put(name,cce.getChild(0).toString());
                                            }
                                            
                                            //We also add in the Name value pair along with everything else
                                            values.put(cce.getAttributeName(q).toString(),cce.getAttributeValue(q).toString());

                                            if(cce.getAttributeName(q).equals("Type"))
                                            {
                                                //We need to determine our global label:
                                                if(cce.getAttributeValue(q).equals("PNG"))
                                                {
                                                    globalHashName = "PngImages";
                                                }
                                                else if(cce.getAttributeValue(q).equals("BMP"))
                                                {
                                                    globalHashName = "BmpImages";
                                                }
                                                else if(cce.getAttributeValue(q).equals("AIFF")) //AIff
                                                {
                                                    globalHashName = "Audio";
                                                }
                                                else if(cce.getAttributeValue(q).equals("JPG"))
                                                {
                                                    globalHashName = "Images";
                                                }
                                                else if(cce.getAttributeValue(q).equals("GIF"))
                                                {
                                                    globalHashName = "Images";
                                                }
                                                else if(cce.getAttributeValue(q).equals("TIFF"))
                                                {
                                                    globalHashName = "Images";
                                                }
                                                else if(cce.getAttributeValue(q).equals("JPEG"))
                                                {
                                                    globalHashName = "Images";
                                                }
                                            }
                                        }
                                    }
                                }
                                //Now that all of the attributes for a given media resource have been captured, we add it to our list:
                                if(values != null)
                                {
                                    if(this.debug)
                                    {
                                        System.out.println("Adding " + name + " to the " + globalHashName + " hashtable.");
                                    }
                
                                    if(globalHashName == "PngImages")
                                        pngTable.put(name,values);
                                    else if(globalHashName == "BmpImages")
                                        bmpTable.put(name,values);
                                    else if(globalHashName == "Audio")
                                        audioTable.put(name,values);
                                    else if(globalHashName == "Images")
                                        imageTable.put(name,values);
                                }
                            }
                        }
                    }
                } 
            }
            //Now we need to add any of our hashtables as required:
            if(!pngTable.isEmpty())
            {
                if(this.debug)
                {
                    System.out.println("There are a total of " + pngTable.size() + " PNG Images.");
                    System.out.println("Adding PNG Images to the guiResourceList");
                }

                this.guiResourceList.put("PngImages",pngTable);
            }
            
            if(!bmpTable.isEmpty())
            {
                if(this.debug)
                {
                    System.out.println("There are a total of " + bmpTable.size() + " BMP Images.");
                    System.out.println("Adding BMP Images to the guiResourceList");
                }

                this.guiResourceList.put("BmpImages",bmpTable);
            }
            
            if(!audioTable.isEmpty())
            {
                if(this.debug)
                {
                    System.out.println("There are a total of " + audioTable.size() + " Audio Files.");
                    System.out.println("Adding Audio Files to the guiResourceList");
                }

                this.guiResourceList.put("Audio",audioTable);
            }
            
            if(!imageTable.isEmpty())
            {
                if(this.debug)
                {
                    System.out.println("There are a total of " + imageTable.size() + " Other Images.");
                    System.out.println("Adding Other Images to the guiResourceList");
                }

                this.guiResourceList.put("Images",imageTable);
            }
        }
        catch(Exception ex)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems unpacking xml Resource List in UnpackXmlGuiResourceList method: "+ex.toString());
                ex.printStackTrace();
            }
        }

        if(guiResourceList.size()>0)
        {
            unpacked = true;
        }

        return unpacked;
    }
    
    public boolean UnpackStaticMediaResourceList(MultiMap resources, MultiMap translations)
    {
        boolean unpacked = false;
        Hashtable values = null;
        String name = "";
        String value = "";
        
        
        try
        {
            for(Enumeration e = resources.elements(); e.hasMoreElements();)
            {
                // get elements
                String k = (String) e.nextElement();
                
                // should have a colon and a location
                if((k.indexOf("_")>=0)&&(k.indexOf(":")>=0)&&(k.indexOf("Location")>=0))
                {
                    name = k.substring(0,k.indexOf("_")-1);
                    String loc = k.substring(k.indexOf(":")); 
                    
                    // get all the network elements
                    for(Enumeration f = translations.elements(new Integer(islandSoftwareEngine.KERNEL_NETWORK)); f.hasMoreElements();)
                    {
                        String l = (String) f.nextElement();
                        
                        if(l.indexOf(loc)>=0)
                        {
                            value = l.substring(l.indexOf("_"));
                    
                            if((name!=null)&&(value!=null))
                            {
                                if((name.length()>0)&&(value.length()>0))
                                {
                                    this.mediaResourceList.put(name,value);
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
                System.out.println("[ERROR] problems unpacking xml Resource List:"+ex.toString());
                ex.printStackTrace();
            }
        }
        if(mediaResourceList.size()>0)
        {
            unpacked = true;
        }
        
        return unpacked;
    }
    
    public boolean UnpackStaticDataResourceList(MultiMap resources, MultiMap translations)
    {
        boolean unpacked = false;
        Hashtable values = null;
        String name = "";
        String value = "";
        
        
        try
        {
            for(Enumeration e = resources.elements(); e.hasMoreElements();)
            {
                // get elements
                String k = (String) e.nextElement();
                
                // should have a colon and a location
                if((k.indexOf("_")>=0)&&(k.indexOf(":")>=0)&&(k.indexOf("Location")>=0))
                {
                    name = k.substring(0,k.indexOf("_")-1);
                    String loc = k.substring(k.indexOf(":")); 
                    
                    // get all the network elements
                    for(Enumeration f = translations.elements(new Integer(islandSoftwareEngine.KERNEL_NETWORK)); f.hasMoreElements();)
                    {
                        String l = (String) f.nextElement();
                        
                        if(l.indexOf(loc)>=0)
                        {
                            value = l.substring(l.indexOf("_"));
                    
                            if((name!=null)&&(value!=null))
                            {
                                if((name.length()>0)&&(value.length()>0))
                                {
                                    this.dataResourceList.put(name,value);
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
                System.out.println("[ERROR] problems unpacking xml Resource List:"+ex.toString());
                ex.printStackTrace();
            }
        }
        if(dataResourceList.size()>0)
        {
            unpacked = true;
        }
        
        return unpacked;
    }

    /**
     * Function returns only the actual data elements
     * and not the xml resource list.
     * @return <description>
     */
    public Enumeration getGuiResourceEnumeration()
    {
        try
        {
            if(guiResourceList.size()>1)
            {
                if(guiResourceList.containsKey(new Integer(islandApplicationResource.XML_GUI_RESOURCE_LIST)))
                {
                    this.xmlGuiResourceList = (String) this.guiResourceList.get(new Integer(islandApplicationResource.XML_GUI_RESOURCE_LIST));
                
                    this.guiResourceList.remove(new Integer(islandApplicationResource.XML_GUI_RESOURCE_LIST));
                }
                
                if(guiResourceList.containsKey(new Integer(islandApplicationResource.XML_GUI_RESOURCE_LIST)))
                {
                    
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting enumeration without xmlResurceList:"+e.toString());
            }
        }
        
        return this.guiResourceList.elements();
    }
    
        /**
     * Function returns only the actual data elements
     * and not the xml resource list.
     * @return <description>
     */
    public Enumeration getMediaResourceEnumeration()
    {
        try
        {
            if(mediaResourceList.size()>1)
            {
                if(mediaResourceList.containsKey(new Integer(islandApplicationResource.XML_MEDIA_RESOURCE_LIST)))
                {
                    this.xmlMediaResourceList = (String) this.mediaResourceList.get(new Integer(islandApplicationResource.XML_MEDIA_RESOURCE_LIST));
                
                    this.mediaResourceList.remove(new Integer(islandApplicationResource.XML_MEDIA_RESOURCE_LIST));
                }
                
                if(mediaResourceList.containsKey(new Integer(islandApplicationResource.STATIC_MEDIA_RESOURCE_LIST)))
                {
                    
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting enumeration without xmlResurceList:"+e.toString());
            }
        }
        
        return this.mediaResourceList.elements();
    }

     /**
     * Function returns only the actual data elements
     * and not the xml resource list.
     * @return <description>
     */
    public Enumeration getDataResourceEnumeration()
    {
        try
        {
            if(dataResourceList.size()>1)
            {
                if(dataResourceList.containsKey(new Integer(islandApplicationResource.XML_DATA_RESOURCE_LIST)))
                {
                    this.xmlDataResourceList = (String) this.dataResourceList.get(new Integer(islandApplicationResource.XML_DATA_RESOURCE_LIST));
                
                    this.dataResourceList.remove(new Integer(islandApplicationResource.XML_DATA_RESOURCE_LIST));
                }
                
                if(dataResourceList.containsKey(new Integer(islandApplicationResource.STATIC_DATA_RESOURCE_LIST)))
                {
                    
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting enumeration without xmlResurceList:"+e.toString());
            }
        }
        
        return this.dataResourceList.elements();
    }

     /**
     * Function returns only the actual data elements
     * and not the xml resource list.
     * @return <description>
     */
    public Enumeration getAddressEnumeration()
    {
        try
        {
            if(this.addressList.size()>1)
            {
                if(this.addressList.containsKey(new Integer(islandApplicationResource.XML_ADDRESS_LIST)))
                {
                    this.xmlAddressList = (String) this.addressList.get(new Integer(islandApplicationResource.XML_ADDRESS_LIST));
                
                    this.addressList.remove(new Integer(islandApplicationResource.XML_ADDRESS_LIST));
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting enumeration without xmlResurceList:"+e.toString());
            }
        }
        
        return this.addressList.elements();
    }

     /**
     * Function returns only the actual data elements
     * and not the xml resource list.
     * @return <description>
     */
    public Enumeration getOperationEnumeration()
    {
        try
        {
            if(this.operationList.size()>1)
            {
                if(this.operationList.containsKey(new Integer(islandApplicationResource.XML_OPERATION_LIST)))
                {
                    this.xmlOperationList = (String) this.operationList.get(new Integer(islandApplicationResource.XML_OPERATION_LIST));
                
                    this.operationList.remove(new Integer(islandApplicationResource.XML_OPERATION_LIST));
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting enumeration without xmlResurceList:"+e.toString());
            }
        }
        
        return this.operationList.elements();
    }

    
     /**
     * Function returns only the actual data elements
     * and not the xml resource list.
     * @return <description>
     */
    public Enumeration getTranslationEnumeration()
    {
        try
        {
            if(this.translationList.size()>1)
            {
                if(this.translationList.containsKey(new Integer(islandApplicationResource.XML_TRANSLATION_LIST)))
                {
                    Enumeration xmlTLE = this.translationList.elements(new Integer(islandApplicationResource.XML_TRANSLATION_LIST));
                
                    for(Enumeration e = xmlTLE; e.hasMoreElements();)
                    {
                        this.xmlTranslationList = (String) e.nextElement();
                    }
                    
                    this.translationList.removeKey(new Integer(islandApplicationResource.XML_TRANSLATION_LIST));
                }
            }
        }
        catch(Exception e)
        {
            if(this.debug)
            {
                System.out.println("[ERROR] problems getting enumeration without xmlTranslationList:"+e.toString());
            }
        }
        
        return this.translationList.elements();
    }
    
    public Enumeration getGuiResourceKeys()
    {
        return this.guiResourceList.keys();
    }
    
    public Object getGuiResourceData(Object key)
    {
        return this.guiResourceList.get(key);
    }
    
    public Enumeration getMediaResourceKeys()
    {
        return this.mediaResourceList.keys();
    }
    
    public Object getMediaResourceData(Object key)
    {
        return this.mediaResourceList.get(key);
    }
    
    public Enumeration getDataResourceKeys()
    {
        return this.dataResourceList.keys();
    }
    
    public Object getDataResourceData(Object key)
    {
        return this.dataResourceList.get(key);
    }
    
    public Enumeration getAddressKeys()
    {
        return this.addressList.keys();
    }
    
    public Object getAddressData(Object key)
    {
        return this.addressList.get(key);
    }
    
    public Enumeration getOperationKeys()
    {
        return this.operationList.keys();
    }
    
    public Object getOperationData(Object key)
    {
        return this.operationList.get(key);
    }
    
    public Enumeration getTranslationKeys()
    {
        return this.translationList.keys();
    }
    
    public Object getTranslationData(Object key)
    {
        return this.translationList.elements(key);
    }
} 

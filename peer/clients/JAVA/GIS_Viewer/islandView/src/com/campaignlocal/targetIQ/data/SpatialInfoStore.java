/*
 * SpatialInfoStore.java
 *
 * Created on June 16, 2007, 12:17 AM
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package com.campaignlocal.targetIQ.data;

import java.awt.Color;
import java.io.IOException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;

import org.geotools.catalog.Catalog;
import org.geotools.catalog.GeoResource;
import org.geotools.catalog.Service;
import org.geotools.catalog.defaults.DefaultCatalog;
import org.geotools.catalog.defaults.DefaultServiceFinder;
import org.geotools.data.FeatureSource;
import org.geotools.data.memory.MemoryDataStore;
import org.geotools.data.postgis.PostgisDataStoreFactory;
import org.geotools.data.shapefile.ShapefileDataStoreFactory;
import org.geotools.data.wfs.WFSDataStoreFactory;
import org.geotools.demo.mappane.MapViewer;
import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
import org.geotools.feature.Feature;
import org.geotools.feature.FeatureType;
import org.geotools.feature.FeatureTypes;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.feature.IllegalAttributeException;
import org.geotools.feature.SchemaException;
import org.geotools.map.MapLayer;
import org.geotools.styling.Graphic;
import org.geotools.styling.Mark;
import org.geotools.styling.SLDParser;
import org.geotools.styling.Style;
import org.geotools.styling.StyleBuilder;
import org.geotools.styling.StyleFactory;
import org.geotools.styling.StyleFactoryFinder;
import org.geotools.styling.Symbolizer;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.Point;

/**
 *
 * @author Administrator
 */
public class SpatialInfoStore {
    
     
    /**
     * The List of Features used to store data outside the localCatalog.
     */
    public List theFeatureList;
//    List <Feature> theFeatureList;
    
    /**
     * The List of FeatureCollections used to store data outside the localCatalog.
     */
    public List  theFeatureCollectionList;
//    List <FeatureCollection> theFeatureCollectionList;
    
	/**
	 * The List of DataStores used to store data outside the localCatalog.
	 */
    
    public List theDataStoreList;
//    List <DataStore> theDataStoreList;
	
    /**
     * The local Catalog used to store the services pointing to data in data stores.
     */
    public Catalog localCatalog;
    
    /**
     * A Network Catalog used to store the services pointing to data on the network.
     */
    public Catalog aNetworkCatalog;
    
    /**
     * The Map of Styles used to render the different layers stored as:
     *   String name, Style s.
     */
    public Map theStyleMap;
   
    
    public MapLayer[] currentActiveLayers;
    
    /** Creates a new instance of ViewerData */
    public SpatialInfoStore() 
    {
        theFeatureList = new LinkedList();
        theFeatureCollectionList = new LinkedList();
        theDataStoreList = new LinkedList();
        localCatalog = new DefaultCatalog();
        aNetworkCatalog = new DefaultCatalog();//TODO: How is this done?
        theStyleMap = new HashMap();
    }
    
	/**
	 * @return The List used to store data as Features.
	 */
	public List getFeaturenList() {
		return theFeatureList;
	}
	
    /**
     * @return The List used to store data in FeatureCollections.
     */
    public List getFeatureCollectionList() {
        return theFeatureCollectionList;
    }
    
    /**
     * @return The List used to store data in DataStores.
     */
    public List getDataStoreList() {
        return theDataStoreList;
    }
    
    /**
     * @return The catalog used to store data.
     */
    public Catalog getLocalCatalog() {
        return localCatalog;
    }
    
}

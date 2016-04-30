/*
 * ViewerManager.java
 *
 * Created on June 16, 2007, 12:27 AM
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package com.campaignlocal.targetIQ.main;

import bsh.This;
import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Rectangle;
import java.awt.geom.AffineTransform;
import java.awt.image.BufferedImage;
import java.awt.image.BufferedImageOp;
import java.awt.*;
import java.awt.event.*;
import java.applet.*;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;

import javax.imageio.ImageIO;
import javax.units.SI;
import javax.swing.JFrame;
import javax.swing.WindowConstants;
import javax.media.jai.PlanarImage;

import org.geotools.catalog.GeoResource;
import org.geotools.catalog.Service;
import org.geotools.catalog.defaults.DefaultServiceFinder;

import org.geotools.data.coverage.grid.AbstractGridFormat;
import org.geotools.data.FeatureSource;
import org.geotools.data.FeatureResults;
import org.geotools.data.FeatureReader;
import org.geotools.data.postgis.PostgisDataStoreFactory;
import org.geotools.data.wfs.WFSDataStoreFactory;
import org.geotools.data.wms.WebMapServer;
import org.geotools.data.wms.WMSUtils;
import org.geotools.data.wms.request.GetMapRequest;
import org.geotools.data.wms.response.GetMapResponse;
import org.geotools.data.wms.gce.WMSGridCoverageExchange;
import org.geotools.data.wms.gce.WMSFormat;
import org.geotools.data.wms.gce.WMSReader;
import org.geotools.data.ows.WMSCapabilities;
import org.geotools.data.ows.Layer;
import org.geotools.data.shapefile.ShapefileDataStore;
import org.geotools.data.shapefile.ShapefileDataStoreFactory;

import org.geotools.demo.mappane.MapViewer;

import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
import org.geotools.feature.Feature;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureCollections;
import org.geotools.feature.FeatureType;
import org.geotools.feature.FeatureTypes;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.feature.IllegalAttributeException;
import org.geotools.feature.SchemaException;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureTypeFactory;

import org.geotools.filter.FilterFactory;
import org.geotools.filter.FilterFactoryImpl;
import org.geotools.filter.LiteralExpression;
import org.geotools.filter.IsEqualsToImpl;
import org.geotools.filter.CompareFilter;

import org.geotools.geometry.jts.ReferencedEnvelope;

import org.geotools.map.DefaultMapLayer;
import org.geotools.map.MapLayer;
import org.geotools.map.DefaultMapContext;
import org.geotools.map.MapContext;

import org.geotools.referencing.FactoryFinder;
import org.geotools.referencing.crs.DefaultGeographicCRS;
import org.geotools.referencing.factory.FactoryGroup;
import org.geotools.referencing.operation.DefaultMathTransformFactory;
import org.geotools.referencing.operation.DefiningConversion;
import org.geotools.referencing.CRS;

import org.geotools.styling.Graphic;
import org.geotools.styling.Mark;
import org.geotools.styling.SLDParser;
import org.geotools.styling.Style;
import org.geotools.styling.StyleBuilder;
import org.geotools.styling.StyleFactory;
import org.geotools.styling.StyleFactoryFinder;
import org.geotools.styling.Symbolizer;
import org.geotools.styling.ColorMap;
import org.geotools.styling.LineSymbolizer;
import org.geotools.styling.PointSymbolizer;
import org.geotools.styling.PolygonSymbolizer;
import org.geotools.styling.RasterSymbolizer;
import org.geotools.styling.LineSymbolizerImpl;
import org.geotools.styling.Rule;
import org.geotools.styling.Symbolizer;
import org.geotools.styling.TextSymbolizer;
import org.geotools.styling.StyleFactoryImpl;
import org.geotools.styling.FeatureTypeStyle;
import org.geotools.styling.ExternalGraphic;

import org.geotools.ows.ServiceException;
import org.geotools.coverage.grid.GridCoverage2D;
import org.geotools.coverage.grid.GridCoverageFactory;
import org.geotools.factory.Hints;
import org.geotools.image.imageio.IIOMetadataDumper;
import org.geotools.resources.TestData;
import org.geotools.renderer.style.GraphicStyle2D;
import org.geotools.geometry.GeneralEnvelope;
import org.geotools.geometry.Geometry;
import org.geotools.parameter.DefaultParameterDescriptorGroup; 
import org.geotools.parameter.ParameterGroup;
import org.geotools.parameter.Parameter;
import org.geotools.gui.swing.JMapPane;
import org.geotools.display.style.DefaultPointSymbolizer;

import org.opengis.referencing.FactoryException;
import org.opengis.referencing.IdentifiedObject;
import org.opengis.referencing.NoSuchIdentifierException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.cs.AxisDirection;
import org.opengis.referencing.cs.CSFactory;
import org.opengis.referencing.cs.CartesianCS;
import org.opengis.referencing.cs.CoordinateSystemAxis;
import org.opengis.referencing.operation.Conversion;

import org.opengis.coverage.Coverage;
import org.opengis.coverage.grid.Format;
import org.opengis.coverage.grid.GridCoverageExchange;
import org.opengis.coverage.grid.GridCoverageReader;
import org.opengis.coverage.grid.GridCoverageReader;

import org.opengis.parameter.ParameterValueGroup;
import org.opengis.parameter.GeneralParameterValue;
import org.opengis.parameter.GeneralParameterDescriptor;
import org.opengis.parameter.ParameterValueGroup;
import org.opengis.parameter.ParameterDescriptor;

import org.opengis.referencing.FactoryException;
import org.opengis.referencing.IdentifiedObject;
import org.opengis.referencing.NoSuchIdentifierException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.cs.AxisDirection;
import org.opengis.referencing.cs.CSFactory;
import org.opengis.referencing.cs.CartesianCS;
import org.opengis.referencing.cs.CoordinateSystemAxis;
import org.opengis.referencing.operation.Conversion;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;

import org.opengis.util.CodeList;

import org.opengis.go.display.style.FillPattern;

import org.xml.sax.SAXException;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Envelope;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.Point;

import com.campaignlocal.targetIQ.gui.Viewer;
import com.campaignlocal.targetIQ.data.SpatialInfoStore;
/**
 *
 * @author Administrator
 */
public class ViewerManager extends Applet implements ActionListener 
{
    
    /* The name of the test shapefile. */
    public final String SHAPEFILENAME = "countries/countries.shp";
    
    /* State boundaries */
    public final String SHAPEFILENAME2 = "67669316/67669316.shp";
    public final String SHAPEFILENAME3 = "63467961/63467961.shp";
    public final String SHAPEFILENAME4 = "69827133/69827133.shp";
    public final String SHAPEFILENAME5 = "85887537/85887537.shp"; 
    
    /* Zip Code Boundaries */
    public final String DC5DIGIT = "US_UrbanAreas/DC_zipcodes/zt11_d00.shp";
    public final String IL5DIGIT = "US_UrbanAreas/IL_zipcodes/zt17_d00.shp";
    public final String TX5DIGIT = "US_UrbanAreas/TX_zipcodes/zt48_d00.shp";
    public final String CA5DIGIT = "US_UrbanAreas/CA_zipcodes/zt06_d00.shp";
    
    /* The shapefile styles */
    public final String SHAPEFILESLDNAME = "countries/countries.sld";
    
    /* local directory for images */
    public final String URBANAREADIR = "C:/TargetIQ/Viewer/Media";
    //public final String URBANAREADIR = "/var/www/html/TargetIQ/Viewer/dist/media/";
    //public final String URBANAREADIR = "http://www.ekendotech.net/TargetIQ/Viewer/dist/media";
    //public final String URBANAREADIR = "/US_UrbanAreas";
    
    /* Cartographic variables */
    public final Envelope NOEDGES = new Envelope(-168.67,17.84,-65.15,71.55);
    public org.opengis.spatialschema.geometry.Envelope CurrentEnvelope;
    
    /* map tile cache */
    public final javax.media.jai.TileCache CACHE = javax.media.jai.JAI.getDefaultInstance().getTileCache();
    
    /* Urban Areas*/
    public enum UrbanArea { HOUSTON, DC, CHICAGO, LEMONT, SANFRAN, LA }
    
    /* Default envelope -179.0,179.0,-80.0,80.0 */
    public ReferencedEnvelope envlp_NoEdges2 = new ReferencedEnvelope(-168.67,17.84,-65.15,71.55, DefaultGeographicCRS.WGS84);
    public ReferencedEnvelope envlp_NoEdges3 = new ReferencedEnvelope(-73.92633884,-73.95209173,40.66593372,40.70507812,DefaultGeographicCRS.WGS84);
    
    public MapLayer tvCoverage;
    public MapLayer radioCoverage;
    public MapLayer demog;
    public MapLayer competitorsCarsSold;
    public MapLayer carsSold;
    
    // TODO: move to viewerGUI
    CoordinateReferenceSystem projCRS = null;
    
    /* ViewerGUI class */
    Viewer viewerGUI;
    
    /* demo class */
    SpatialInfoStore viewerData;
    
    UrbanArea selectedAerea = UrbanArea.SANFRAN;
    
    /** Creates a new instance of ViewerBase */
    public ViewerManager() 
    {
        viewerData = new SpatialInfoStore();
        
        
    }
    
    public void LoadDefaults() throws Exception
    {
        viewerGUI.initialize_JMapPane();
        //viewerGUI.jmp.setMapArea(NOEDGES);
        CACHE.setMemoryCapacity(64 * 1024 * 1024);
        System.out.println("initialized JMap Pane");
        
        this.loadCountryShapesAndStyles();
        
        DrawMap(UrbanArea.SANFRAN);
        //DrawMap(UrbanArea.HOUSTON);
        //System.out.println("set houston as Default");
        
        //viewerGUI.jmp.setReset(true);
        //viewerGUI.jmp.repaint();
        
        /* Paint */
        viewerGUI.frame.repaint();
        viewerGUI.frame.doLayout();
    }
    
    public void DrawMap(UrbanArea area) throws Exception
    {
        
        if(area.equals(UrbanArea.HOUSTON))
        {
            this.loadHoustonInfo();
        }
        
        if(area.equals(UrbanArea.CHICAGO))
        {
            this.loadChicagoInfo();
        }
        
        if(area.equals(UrbanArea.DC))
        {
            this.loadDCInfo();
        } 

        if(area.equals(UrbanArea.LEMONT))
        {
            this.loadLemontInfo();
        }
        
        if(area.equals(UrbanArea.SANFRAN))
        {
            this.loadSanFranciscoInfo();
        }
        
        /* Paint */
        viewerGUI.frame.repaint();
        viewerGUI.frame.doLayout();
    }

    /**
     * Gets a FeatureCollection with the shapefile from the catalog.
     * <p>
     * This method <b>must</b> be called after {@link #loadShapefileIntoCatalog(String)}.
     * </p>
     * @return The shapefile feature source.
     * 
     * @throws IOException Any I/O errors that occur accessing the shapefile resource.
     */
    public FeatureSource getAShapefileFeatureSourceFromCatalog(String ShapeFileName){
//    public FeatureCollection getFeatureCollectionForShapefile() throws IOException {
        
        //create the uri to lookup
        URI uri = null;
        try 
        {
            System.out.println(ShapeFileName);
            uri =  new URI( this.viewerData.getClass().getResource( ShapeFileName ).toString() );
            System.out.println(uri.toString());
        } 
        catch ( URISyntaxException uriex )
        {
            System.err.println( "Unable to create shapefile uri"+ uriex.getMessage() );
            //throw (IOException) new IOException( "Unable to create shapefile uri").initCause( uriex );
        }
        
        
        //lookup service, should be only one
        List serviceList = viewerData.localCatalog.find( uri, null );
        Service service = (Service) serviceList.get( 0 );
        
        //shapefiles only contain a single resource
        List resourceList = null;
        try
        {
            resourceList = service.members( null );
        } 
        catch (IOException ioex)
        {
            System.err.println("An IOException occurred on service resolve: " + ioex.getMessage() );
        }
        
        GeoResource resource = (GeoResource) resourceList.get( 0 );
        
        FeatureSource shpFS = null;
        try{
            shpFS = (FeatureSource) resource.resolve( FeatureSource.class, null );
        } catch (IOException ioex){
            System.err.println("IOException on resoloving shape resource to FeatureSource: " + ioex.getMessage() );
        }
        return shpFS;
    }
    
    /**
     *
     */
    public void loadSpatialData()
    {
       
         /* load country ShapeFiles */
        loadShapefileIntoCatalog(SHAPEFILENAME);
        
        if(selectedAerea.equals(UrbanArea.CHICAGO))
        {
            loadShapefileIntoCatalog(SHAPEFILENAME2);
            loadShapefileIntoCatalog(IL5DIGIT);
    
        }

        if(selectedAerea.equals(UrbanArea.DC))
        {
            loadShapefileIntoCatalog(SHAPEFILENAME3);
            loadShapefileIntoCatalog(DC5DIGIT);

        }
        
        if(selectedAerea.equals(UrbanArea.HOUSTON))
        {
            loadShapefileIntoCatalog(SHAPEFILENAME4); 
            loadShapefileIntoCatalog(TX5DIGIT);
        }
       
    
        if(selectedAerea.equals(UrbanArea.LEMONT))
        {
            loadShapefileIntoCatalog(SHAPEFILENAME5);
            loadShapefileIntoCatalog(IL5DIGIT);
    
        }
        
        if(selectedAerea.equals((UrbanArea.SANFRAN)))
        {
            loadShapefileIntoCatalog(CA5DIGIT);
        }
    }

    /**
     * Loads a shapefile service into the catalog.
     * 
     * @throws IOException Any I/O errors loading into the catalog.
     */
    public void loadShapefileIntoCatalog(String shpname)
    {
    
        try
        {
            //create shapefile datastore parameters
            System.out.println(shpname);

            URL shapefileURL = this.viewerData.getClass().getResource( shpname );

            System.out.println(shapefileURL.toString());

            Map params = new HashMap();
            params.put( ShapefileDataStoreFactory.URLP.key, shapefileURL );

            System.out.println(params.toString());

            //load the services, there should be only one service
            DefaultServiceFinder finder = new DefaultServiceFinder( viewerData.localCatalog );
            List services = finder.aquire( params );

            System.out.println(viewerData.localCatalog.toString());

            //add the service to the catalog
            viewerData.localCatalog.add( (Service) services.get( 0 ) );

        }
        catch(Exception e)
        {
            System.out.println("ERROR[can't load shapefiles]:"+e.toString());
        }
        
        System.out.println("done loading shapefile into catalog");
    }
    
    public void createFeatureStyles(String styleMapName)
    {
        if(styleMapName.equals("shpstyl"))
        {
            Style shpstyl = createShapefileStyleFromSLDFile(SHAPEFILESLDNAME);
            viewerData.theStyleMap.put(styleMapName,shpstyl);
        }
        
        if(styleMapName.equals("shpstyl2")||
                styleMapName.equals("shpstyl3")||
                styleMapName.equals("shpstyl4")||
                styleMapName.equals("shpstyl5"))
        {
            StyleBuilder sb = new StyleBuilder();
            LineSymbolizer ls =  sb.createLineSymbolizer(Color.BLACK,1);
            Style statesStyle = sb.createStyle(ls);
            viewerData.theStyleMap.put(styleMapName,statesStyle);
        }
        
        if(styleMapName.equals("dcZipStyles")||
                styleMapName.equals("ilZipStyles")||
                styleMapName.equals("txZipStyles")||
                styleMapName.equals("caZipStyles"))
        {
            StyleBuilder sb = new StyleBuilder();
            LineSymbolizer ls =  sb.createLineSymbolizer(Color.RED,1);
            TextSymbolizer ts = sb.createTextSymbolizer(
                    Color.WHITE,
                    sb.createFont("Lucida Sans", 10),
                    "NAME");
            Style zipCodeStyle = sb.createStyle(ls);
            Style zipCodeLabelStyle = sb.createStyle(ts);
            //viewerData.theStyleMap.put(styleMapName,zipCodeStyle);
            viewerData.theStyleMap.put(styleMapName,zipCodeLabelStyle);
            viewerData.theStyleMap.put("txZipBndry",zipCodeStyle);
            
            System.out.println("added zip style");
        }
    }
    
    public void competitorsCarsSold(boolean adding) throws Exception
    {
         if(this.viewerGUI!=null)
        {
            if(this.viewerGUI.context!=null)
            {
                
                FeatureCollection fc = FeatureCollections.newCollection();
                StyleBuilder sb = new StyleBuilder();
                Mark cross = sb.createMark(StyleBuilder.MARK_CROSS, Color.WHITE);    
                ExternalGraphic eg = sb.createExternalGraphic((new File(URBANAREADIR+"/whiteCar.gif")).toURL(),"image/gif");
                Graphic graph = sb.createGraphic(eg, null, null, 1, 10, 0);
                Symbolizer s = sb.createPointSymbolizer(graph);
                Style styleCompetitorsCars = sb.createStyle( s );
                styleCompetitorsCars.setName("Competitors");      
                    
                if(this.selectedAerea.equals(UrbanArea.HOUSTON))
                {
                    
                    AttributeType[] types = new AttributeType[1];
                    types[0] = AttributeTypeFactory.newAttributeType("centre", Point.class);
                
                    // feature type creation
                    FeatureType pointType = FeatureTypeFactory.newFeatureType(types,"pointfeature");
                
                    // we could use also the WKT writers instead 
                    GeometryFactory geomFac = new GeometryFactory();
                    Envelope env = this.viewerGUI.jmp.getMapArea();
                    
                    //System.out.println("centre coord:"+env.centre().toString());
                    //System.out.println("centre coord-X:"+env.centre().x+" coord-Y:"+env.centre().y);
                    
                                                                     // 43000       71000   
                    Point point1 = geomFac.createPoint(new Coordinate(-95.39000, 29.74350));
                    Point point2 = geomFac.createPoint(new Coordinate(-95.39100, 29.74250));
                    Point point3 = geomFac.createPoint(new Coordinate(-95.39200, 29.74150));
                    Point point4 = geomFac.createPoint(new Coordinate(-95.39300, 29.74050));
                    Point point5 = geomFac.createPoint(new Coordinate(-95.39555, 29.73999));
                    
                    Feature feature1 = pointType.create(new Object[] { point1 });
                    Feature feature2 = pointType.create(new Object[] { point2 });
                    Feature feature3 = pointType.create(new Object[] { point3 });
                    Feature feature4 = pointType.create(new Object[] { point4 });
                    Feature feature5 = pointType.create(new Object[] { point5 });
                
                    //feature collection creation
                    fc.add(feature1);	
                    fc.add(feature2);
                    fc.add(feature3);
                    fc.add(feature4);
                    fc.add(feature5);
     
                }
                
                if(this.selectedAerea.equals(UrbanArea.SANFRAN))
                {
                    
                    AttributeType[] types = new AttributeType[1];
                    types[0] = AttributeTypeFactory.newAttributeType("centre", Point.class);
                
                    // feature type creation
                    FeatureType pointType = FeatureTypeFactory.newFeatureType(types,"pointfeature");
                
                    // we could use also the WKT writers instead 
                    GeometryFactory geomFac = new GeometryFactory();
                    Envelope env = this.viewerGUI.jmp.getMapArea();
                    
                    //System.out.println("centre coord:"+env.centre().toString());
                    //System.out.println("centre coord-X:"+env.centre().x+" coord-Y:"+env.centre().y);
                    
                                               //-122.50067=>-122.46436       37.73022=>37.75906   
                    Point point1 = geomFac.createPoint(new Coordinate(-122.50067, 37.73022));
                    Point point2 = geomFac.createPoint(new Coordinate(-122.50000, 37.74250));
                    Point point3 = geomFac.createPoint(new Coordinate(-122.45000, 37.74000));
                    Point point4 = geomFac.createPoint(new Coordinate(-122.45509, 37.75050));
                    Point point5 = geomFac.createPoint(new Coordinate(-122.46209, 37.73999));
                    
                    Feature feature1 = pointType.create(new Object[] { point1 });
                    Feature feature2 = pointType.create(new Object[] { point2 });
                    Feature feature3 = pointType.create(new Object[] { point3 });
                    Feature feature4 = pointType.create(new Object[] { point4 });
                    Feature feature5 = pointType.create(new Object[] { point5 });
                
                    //feature collection creation
                    fc.add(feature1);	
                    fc.add(feature2);
                    fc.add(feature3);
                    fc.add(feature4);
                    fc.add(feature5);
     
                }
                // Add Style to Layer 
                    MapLayer m0 = new DefaultMapLayer(fc,styleCompetitorsCars);
                    
                    if(adding)
                    {
                        this.viewerGUI.context.addLayer(m0);
                        this.competitorsCarsSold = m0;
                        System.out.println("added competitors cars sold coverage:");
                    }
                    else
                    {
                        if(competitorsCarsSold!=null)
                        {
                            this.viewerGUI.context.removeLayer(this.competitorsCarsSold);
                            System.out.println("added competitors cars sold coverage");
                    }   }
                    
                    this.viewerGUI.jmp.setReset(true);
                    this.viewerGUI.jmp.repaint();
            }
        }
    }
    
    public void carsSold(boolean adding) throws Exception
    {
        if(this.viewerGUI!=null)
        {
            if(this.viewerGUI.context!=null)
            {
                FeatureCollection fc = FeatureCollections.newCollection();
                StyleBuilder sb = new StyleBuilder();
                Mark cross = sb.createMark(StyleBuilder.MARK_CROSS, Color.BLACK);
                ExternalGraphic eg = sb.createExternalGraphic((new File(URBANAREADIR+"/blackCar.gif")).toURL(),"image/gif");
                Graphic graph = sb.createGraphic(eg, null, null, 1, 10, 0);
                Symbolizer s = sb.createPointSymbolizer(graph);
                Style styleCars = sb.createStyle( s );
                styleCars.setName("Competitors");
                
                if(this.selectedAerea.equals(UrbanArea.HOUSTON))
                {
                    
                    AttributeType[] types = new AttributeType[1];
                    types[0] = AttributeTypeFactory.newAttributeType("centre", Point.class);
                
                    // feature type creation
                    FeatureType pointType = FeatureTypeFactory.newFeatureType(types,"pointfeature");
                
                    // we could use also the WKT writers instead 
                    GeometryFactory geomFac = new GeometryFactory();
                    Envelope env = this.viewerGUI.jmp.getMapArea();  
                                                                     //   29200     77000   
                    Point point1 = geomFac.createPoint(new Coordinate(-95.32600, 29.80350));
                    Point point2 = geomFac.createPoint(new Coordinate(-95.32500, 29.79250));
                    Point point3 = geomFac.createPoint(new Coordinate(-95.32400, 29.79150));
                    Point point4 = geomFac.createPoint(new Coordinate(-95.32500, 29.78050));
                    Point point5 = geomFac.createPoint(new Coordinate(-95.30200, 29.77999));
                  
                    Feature feature1 = pointType.create(new Object[] { point1 });
                    Feature feature2 = pointType.create(new Object[] { point2 });
                    Feature feature3 = pointType.create(new Object[] { point3 });
                    Feature feature4 = pointType.create(new Object[] { point4 });
                    Feature feature5 = pointType.create(new Object[] { point5 });
              
                    //feature collection creation
                    fc = FeatureCollections.newCollection();
                    fc.add(feature1);	
                    fc.add(feature2);
                    fc.add(feature3);
                    fc.add(feature4);
                    fc.add(feature5);
                }

                if(this.selectedAerea.equals(UrbanArea.SANFRAN))
                {
                    
                  
                    AttributeType[] types = new AttributeType[1];
                    types[0] = AttributeTypeFactory.newAttributeType("centre", Point.class);
                
                    // feature type creation
                    FeatureType pointType = FeatureTypeFactory.newFeatureType(types,"pointfeature");
                
                    // we could use also the WKT writers instead 
                    GeometryFactory geomFac = new GeometryFactory();
                    Envelope env = this.viewerGUI.jmp.getMapArea();  
                                            //-122.42804=>-122.39173     37.78790=>37.75906   
                    Point point1 = geomFac.createPoint(new Coordinate(-122.42804, 37.78790));
                    Point point2 = geomFac.createPoint(new Coordinate(-122.40790, 37.77250));
                    Point point3 = geomFac.createPoint(new Coordinate(-122.40400, 37.76150));
                    Point point4 = geomFac.createPoint(new Coordinate(-122.41500, 37.78050));
                    Point point5 = geomFac.createPoint(new Coordinate(-122.39200, 37.77999));
                
                    Feature feature1 = pointType.create(new Object[] { point1 });
                    Feature feature2 = pointType.create(new Object[] { point2 });
                    Feature feature3 = pointType.create(new Object[] { point3 });
                    Feature feature4 = pointType.create(new Object[] { point4 });
                    Feature feature5 = pointType.create(new Object[] { point5 });
                
                    //feature collection creation
                    fc = FeatureCollections.newCollection();
                    fc.add(feature1);	
                    fc.add(feature2);
                    fc.add(feature3);
                    fc.add(feature4);
                    fc.add(feature5);
                }
               
                // Add Style to Layer 
                MapLayer m0 = new DefaultMapLayer(fc,styleCars);
                    
                if(adding)
                {
                    this.viewerGUI.context.addLayer(m0);
                    this.carsSold = m0;
                    System.out.println("added cars sold");
                }
                else
                {
                    this.viewerGUI.context.removeLayer(this.carsSold);
                    System.out.println("added cars sold ");
                }
                    
                this.viewerGUI.jmp.setReset(true);
                this.viewerGUI.jmp.repaint();
                
            }
        }
    }
    
    public void tvCoverage(boolean adding) throws Exception
    {
        if(this.viewerGUI!=null)
        {
            if(this.viewerGUI.context!=null)
            {
                FeatureCollection fc = FeatureCollections.newCollection();
                StyleBuilder sb = new StyleBuilder();
                Mark circle = sb.createMark(StyleBuilder.MARK_CIRCLE, Color.WHITE);
                
                /*
                DefaultPointSymbolizer dps = new DefaultPointSymbolizer();
                dps.setFillPattern(FillPattern.HORIZONTAL_LINES);
                */
                Graphic graph = sb.createGraphic(null,circle, null, 0.2, 75, 0);
                Symbolizer s = sb.createPointSymbolizer(graph);
                
                TextSymbolizer ts = sb.createStaticTextSymbolizer(
                        Color.BLACK,
                        sb.createFont("Lucida Sans", 10),
                        "TV"); 
                Style styleTV = sb.createStyle( s );
                styleTV.setName("TV");
                styleTV.addFeatureTypeStyle(sb.createFeatureTypeStyle(null,ts)); 
                  
                if(this.selectedAerea.equals(UrbanArea.HOUSTON))
                {
                    AttributeType[] types = new AttributeType[1];
                    types[0] = AttributeTypeFactory.newAttributeType("centre", Point.class);
                
                    // feature type creation
                    FeatureType pointType = FeatureTypeFactory.newFeatureType(types,
				"pointfeature");
                
                    // we could use also the WKT writers instead 
                    GeometryFactory geomFac = new GeometryFactory();
                    Envelope env = this.viewerGUI.jmp.getMapArea();
                    
                    //System.out.println("centre coord:"+env.centre().toString());
                    //System.out.println("centre coord-X:"+env.centre().x+" coord-Y:"+env.centre().y);
                    
                    
                    Point point1 = geomFac.createPoint(new Coordinate(-95.35897, 29.75274));
                    //Point point2 = geomFac.createPoint(new Coordinate((10.0), 10.0));
                    Feature feature1 = pointType.create(new Object[] { point1 });
                    //Feature feature2 = pointType.create(new Object[] { point2 });
                
                    //feature collection creation
                    fc.add(feature1);	
                    //fc.add(feature2);
                }
                
                if(this.selectedAerea.equals(UrbanArea.SANFRAN))
                {
                       AttributeType[] types = new AttributeType[1];
                    types[0] = AttributeTypeFactory.newAttributeType("centre", Point.class);
                
                    // feature type creation
                    FeatureType pointType = FeatureTypeFactory.newFeatureType(types,
				"pointfeature");
                
                    // we could use also the WKT writers instead 
                    GeometryFactory geomFac = new GeometryFactory();
                    Envelope env = this.viewerGUI.jmp.getMapArea();
                    
                    //System.out.println("centre coord:"+env.centre().toString());
                    //System.out.println("centre coord-X:"+env.centre().x+" coord-Y:"+env.centre().y);
                    
                    
                    Point point1 = geomFac.createPoint(new Coordinate(-122.42804, 37.75906));
                    Point point2 = geomFac.createPoint(new Coordinate(-122.42804, 37.78790));
                    Point point3 = geomFac.createPoint(new Coordinate(-122.42804, 37.80000));
                    Point point4 = geomFac.createPoint(new Coordinate(-122.49067, 37.78790));
                    Point point5 = geomFac.createPoint(new Coordinate(-122.48804, 37.80000));
                    
                    
                    Feature feature1 = pointType.create(new Object[] { point1 });
                    Feature feature2 = pointType.create(new Object[] { point2 });
                    Feature feature3 = pointType.create(new Object[] { point3 });
                    Feature feature4 = pointType.create(new Object[] { point4 });
                    Feature feature5 = pointType.create(new Object[] { point5 });
                
                    
                    //feature collection creation
                    fc.add(feature1);	
                    fc.add(feature2);
                    fc.add(feature3);
                    fc.add(feature4);
                    fc.add(feature5);
                
                }
                
                    // Add Style to Layer 
                    MapLayer m0 = new DefaultMapLayer(fc,styleTV);
                    
                    if(adding)
                    {
                        this.viewerGUI.context.addLayer(m0);
                        this.tvCoverage = m0;
                        System.out.println("added tv coverage");
                    }
                    else
                    {
                         this.viewerGUI.context.removeLayer(this.tvCoverage);
                        System.out.println("added tv coverage");
                    }
                    
                    this.viewerGUI.jmp.setReset(true);
                    this.viewerGUI.jmp.repaint(); 
                 
            }
         }
    }
    
    public void radioCoverage(boolean adding) throws Exception
    {
        if(this.viewerGUI!=null)
        {
            if(this.viewerGUI.context!=null)
            {
                FeatureCollection fc = FeatureCollections.newCollection();
                StyleBuilder sb = new StyleBuilder();
                Mark circle = sb.createMark(StyleBuilder.MARK_CIRCLE, Color.YELLOW);
                Graphic graph = sb.createGraphic(null, circle, null, .5, 35, 0);
                Symbolizer s = sb.createPointSymbolizer(graph);
                TextSymbolizer ts = sb.createStaticTextSymbolizer(
                        Color.BLACK,
                        sb.createFont("Lucida Sans", 10),
                        "R"); 
                Style styleRadio = sb.createStyle( s );
                styleRadio.setName("Radio");
                styleRadio.addFeatureTypeStyle(sb.createFeatureTypeStyle(null,ts)); 
                
                
                if(this.selectedAerea.equals(UrbanArea.HOUSTON))
                {
                
                    AttributeType[] types = new AttributeType[1];
                    types[0] = AttributeTypeFactory.newAttributeType("centre", Point.class);
                
                    // feature type creation
                    FeatureType pointType = FeatureTypeFactory.newFeatureType(types,
				"pointfeature");
                
                    // we could use also the WKT writers instead 
                    GeometryFactory geomFac = new GeometryFactory(); 
                    Envelope env = this.viewerGUI.jmp.getMapArea();
                    
                    //this.CurrentEnvelope.
                    Point point1 = geomFac.createPoint(new Coordinate(env.centre()));
                    //Point point2 = geomFac.createPoint(new Coordinate((10.0), 10.0));
                    Feature feature1 = pointType.create(new Object[] { point1 });
                    //Feature feature2 = pointType.create(new Object[] { point2 });
               
                    //feature collection creation
                    fc.add(feature1);	
                    //fc.add(feature2);
                }
                
                if(this.selectedAerea.equals(UrbanArea.SANFRAN))
                {
                    AttributeType[] types = new AttributeType[1];
                    types[0] = AttributeTypeFactory.newAttributeType("centre", Point.class);
                
                    // feature type creation
                    FeatureType pointType = FeatureTypeFactory.newFeatureType(types,
				"pointfeature");
                
                    // we could use also the WKT writers instead 
                    GeometryFactory geomFac = new GeometryFactory(); 
                    //Envelope env = this.viewerGUI.jmp.getMapArea();
                    
                    Point point1 = geomFac.createPoint(new Coordinate(-122.46436, 37.78790));
                    Point point2 = geomFac.createPoint(new Coordinate(-122.45804, 37.77790));
                    Point point3 = geomFac.createPoint(new Coordinate(-122.44804, 37.7600));
                    Point point4 = geomFac.createPoint(new Coordinate(-122.43804, 37.75906));
                    Point point5 = geomFac.createPoint(new Coordinate(-122.45504, 37.75590));
                    Point point6 = geomFac.createPoint(new Coordinate(-122.46004, 37.74440));
                    Point point7 = geomFac.createPoint(new Coordinate(-122.42804, 37.73022));
                    
                    
                    Feature feature1 = pointType.create(new Object[] { point1 });
                    Feature feature2 = pointType.create(new Object[] { point2 });
                    Feature feature3 = pointType.create(new Object[] { point3 });
                    Feature feature4 = pointType.create(new Object[] { point4 });
                    Feature feature5 = pointType.create(new Object[] { point5 });
                    Feature feature6 = pointType.create(new Object[] { point6 });
                    Feature feature7 = pointType.create(new Object[] { point7 });
                
               
                    //feature collection creation
                    fc.add(feature1);	
                    fc.add(feature2);
                    fc.add(feature3);	
                    fc.add(feature4);
                    fc.add(feature5);	
                    fc.add(feature6);
                    fc.add(feature7);
                
                }
                
                // Add Style to Layer 
                MapLayer m0 = new DefaultMapLayer(fc,styleRadio);
                   
                    if(adding)
                    {    
                        this.viewerGUI.context.addLayer(m0);
                        System.out.println("added radio coverage");
                        this.radioCoverage = m0;
                        System.out.println("RadioLayerIndex");
                    }
                    else
                    {
                        if(this.radioCoverage!=null)
                        {
                            this.viewerGUI.context.removeLayer(this.radioCoverage);
                            System.out.println("removed radio coverage");
                        }
                     }
                    
                    this.viewerGUI.jmp.setReset(true);
                    this.viewerGUI.jmp.repaint(); 
                 
            }
         }
    }
    
    public void addDemographicLayer(int weight1,int weight2) throws Exception
    {
        if(this.viewerGUI!=null)
        {
            if(this.viewerGUI.context!=null)
            {
               
                if((this.demog!= null))
                {
                     this.viewerGUI.context.removeLayer(demog);
                }
                
                
                //System.out.println("layerCount:"+this.viewerGUI.context.getLayerCount());
                StyleBuilder sb = new StyleBuilder();
                FeatureSource shpDemog = null;
                int wieghtNum = 0;
                CompareFilter dzipFilter = null;
                PolygonSymbolizer ps = null;
                
                // Create Feature Type Style
                StyleFactoryImpl styleFactory = new StyleFactoryImpl();
                
                //Style styleDemog = styleFactory.getDefaultStyle();
                Style styleDemog = sb.createStyle();
                
                
                // Get FeatureSource
                if(this.selectedAerea.equals(UrbanArea.HOUSTON))
                {
                    shpDemog = getAShapefileFeatureSourceFromCatalog(TX5DIGIT);
                }
                
                if(this.selectedAerea.equals(UrbanArea.SANFRAN))
                {
                    shpDemog = getAShapefileFeatureSourceFromCatalog(CA5DIGIT);
                }

                // Get Results
                FeatureResults fsShape = shpDemog.getFeatures();
                FeatureReader reader = fsShape.reader();
                FeatureCollection col = shpDemog.getFeatures();
                
                while (reader.hasNext()) 
                {
                    Feature feature = reader.next();
                    //System.out.println("NAME:"+feature.getAttribute("NAME")+"-DAGE:"+feature.getAttribute("DAGE")+"-DNUM:"+feature.getAttribute("DNUM"));
                    String zip = (String) feature.getAttribute("NAME");
                    Long age = new Long(feature.getAttribute("DAGE").toString());
                    Long num = new Long(feature.getAttribute("DNUM").toString());
                    Color c = null;
                    Rule rZip = null;
                    
                    wieghtNum = (weight1 * age.intValue()) + (weight2 * num.intValue());
                    
                    // 1 black, 6 BGR, 1 white
                    if((wieghtNum >= 0)&&(wieghtNum < 1000))
                    {
                        c = new Color(0,0,0); 
                    }

                    // B0
                    if((wieghtNum >= 1000)&&(wieghtNum < 2000))
                    {
                        c = new Color(0,0,21); 
                    }
                    
                    // B1
                    if((wieghtNum >= 2000)&&(wieghtNum < 3000))
                    {
                        c = new Color(0,0,43); 
                    }
                    
                    // B2
                    if((wieghtNum >= 3000)&&(wieghtNum < 4000))
                    {
                        c = new Color(0,0,86); 
                    }
                    
                    // B3
                    if((wieghtNum >= 4000)&&(wieghtNum < 5000))
                    {
                        c = new Color(0,0,129); 
                    }
                    
                    // B4
                    if((wieghtNum >= 5000)&&(wieghtNum < 6000))
                    {
                        c = new Color(0,0,172); 
                    }
                    
                    // B5
                    if((wieghtNum >= 6000)&&(wieghtNum < 7000))
                    {
                        c = new Color(0,0,215); 
                    }
                    
                    // B6
                    if((wieghtNum >= 7000)&&(wieghtNum < 8000))
                    {
                        c = new Color(0,0,255); 
                    }
                    
                    // G1
                    if((wieghtNum >= 8000)&&(wieghtNum < 9000))
                    {
                        c = new Color(0,43,215); 
                    }
                    
                    // G2
                    if((wieghtNum >= 9000)&&(wieghtNum < 10000))
                    {
                        c = new Color(0,86,172); 
                    }
                    
                    // G3
                    if((wieghtNum >= 10000)&&(wieghtNum < 11000))
                    {
                        c = new Color(0,129,129); 
                    }
                    
                    // G4
                    if((wieghtNum >= 11000)&&(wieghtNum < 12000))
                    {
                        c = new Color(0,172,86); 
                    }
                    
                    // G5
                    if((wieghtNum >= 12000)&&(wieghtNum < 13000))
                    {
                        c = new Color(0,215,43); 
                    }
                    
                    // G6
                    if((wieghtNum >= 13000)&&(wieghtNum < 14000))
                    {
                        c = new Color(0,255,0); 
                    }
                    
                    // R1
                    if((wieghtNum >= 14000)&&(wieghtNum < 15000))
                    {
                        c = new Color(43,215,0); 
                    }
                    
                    // R2
                    if((wieghtNum >= 15000)&&(wieghtNum < 16000))
                    {
                        c = new Color(86,172,0); 
                    }
                    
                    // R3
                    if((wieghtNum >= 16000)&&(wieghtNum < 17000))
                    {
                        c = new Color(129,129,0); 
                    }
                    
                    // R4
                    if((wieghtNum >= 17000)&&(wieghtNum < 18000))
                    {
                        c = new Color(172,86,0); 
                    }
                    
                    // R5
                    if((wieghtNum >= 18000)&&(wieghtNum < 19000))
                    {
                        c = new Color(215,43,0); 
                    }
                    
                    // R6
                    if((wieghtNum >= 19000)&&(wieghtNum < 20000))
                    {
                        c = new Color(255,0,0); 
                    }
                    
                    //System.out.println("WeightNumber:"+wieghtNum);
                    
                    // Create polygon color
                    ps = sb.createPolygonSymbolizer(sb.createStroke(Color.WHITE,1),sb.createFill(c,0.2));
               
                    // Create Rule to Apply Filters
                    rZip = sb.createRule(ps);

                    FeatureType featureType = shpDemog.getFeatures().getSchema();
                    FilterFactoryImpl filterFactory = new FilterFactoryImpl();
                
                    // Get the demographic Zip
                    dzipFilter = filterFactory.createCompareFilter(CompareFilter.COMPARE_EQUALS);
                    dzipFilter.addLeftValue(filterFactory.createAttributeExpression("NAME"));
                    dzipFilter.addRightValue(filterFactory.createLiteralExpression(zip));

                    col = shpDemog.getFeatures(dzipFilter);
                    //System.out.println(weight1+"# zip codes (DAGE filter)= "+col.size());
                    rZip.setFilter(dzipFilter);
                
                    if(zip.equals("94102")||zip.equals("94103")||zip.equals("94104")||zip.equals("94105")||
                            zip.equals("94106")||zip.equals("94107")||zip.equals("94108")||zip.equals("94109")||zip.equals("94110")||
                            zip.equals("94111")||zip.equals("94112")||zip.equals("94113")||zip.equals("94114")||zip.equals("94115")||
                            zip.equals("94116")||zip.equals("94117")||zip.equals("94118")||zip.equals("94119")||zip.equals("94120")||
                            zip.equals("94121")||zip.equals("94122")||zip.equals("94123")||zip.equals("94124")||zip.equals("94125")||
                            zip.equals("94126")||zip.equals("94127")||zip.equals("94128")||zip.equals("94129")||zip.equals("94130")||
                            zip.equals("94131")||zip.equals("94132")||zip.equals("94133")||zip.equals("94134")||zip.equals("94135"))
                    {
                        // Add Feature Type Style to Style
                        styleDemog.addFeatureTypeStyle(sb.createFeatureTypeStyle(null,rZip));
                        //System.out.println("added zip:"+zip);
                    }
                }
                reader.close();
                
                // Add Style to Layer 
                MapLayer m0 = new DefaultMapLayer(shpDemog,styleDemog);
               
                this.viewerGUI.context.addLayer(m0);
                this.demog = m0;
                System.out.println("added demog layer of colored zips");
                
                this.viewerGUI.jmp.setReset(true);
                this.viewerGUI.jmp.repaint(); 
            }
        }
    }
    
    public Style createShapefileStyleFromSLDFile(String shpSLDfile)
    {
        // Make the sldURL from the sldName 
        URL sldURL = this.viewerData.getClass().getResource( shpSLDfile );
        
        //System.out.println(shpSLDfile);
        //System.out.println(sldURL);
        
        // Create the shapefile Style, uses StyleFactory and an SLD URL
        StyleFactory sf = StyleFactoryFinder.createStyleFactory();
        SLDParser stylereader = null;
        try 
        {
            stylereader = new SLDParser(sf,sldURL);
        } 
        catch (IOException ioex)
        {
            System.out.println("IOException on SLDfile read: " + ioex);
        }
        
        Style[] shpStylArr = stylereader.readXML();
        Style shpStyle = shpStylArr[0];
        
        //System.out.println("done creating styles");
        
        return shpStyle;
        
    }
    
    public void loadWMSImage() throws Exception
    {
        URL server = new URL("http://terraservice.net/ogccapabilities.ashx?version=1.1.1&request=GetCapabilties");
        //URL server = new URL("http://www2.dmsolutions.ca/cgi-bin/mswms_gmap?VERSION=1.1.0&REQUEST=GetCapabilities");
        //URL server = new URL("http://www2.demis.nl/mapserver/Request.asp?Service=WMS&WMS=WorldMap&Version=1.1.1&Request=GetCapabilities");
        
        WMSGridCoverageExchange exchange = new WMSGridCoverageExchange(server);

        WMSReader reader = (WMSReader) exchange.getReader(server);
        WMSFormat wmsFormat = (WMSFormat) reader.getFormat();
    
    
        ParameterValueGroup valueGroup = reader.getFormat().getReadParameters();
        DefaultParameterDescriptorGroup descriptorGroup =  (DefaultParameterDescriptorGroup) valueGroup.getDescriptor();

        //System.out.print("valueGroup:"+valueGroup);
        
        
        List paramDescriptors = descriptorGroup.descriptors();
        //System.out.println("descriptorGroup:"+paramDescriptors);
        GeneralParameterValue[] generalParameterValues = new GeneralParameterValue[paramDescriptors.size()];

        for (int i = 0; i < paramDescriptors.size(); i++) 
        {
            GeneralParameterDescriptor paramDescriptor = (GeneralParameterDescriptor) paramDescriptors.get(i);
            GeneralParameterValue generalParameterValue = paramDescriptor.createValue();
            generalParameterValues[i] = generalParameterValue;

            String parameterName = paramDescriptor.getName().getCode();
            
            //System.out.println("Service Param Name:"+parameterName);
        
            
            if (parameterName.equals("LAYERS")) 
            {
                ParameterGroup groupValue = (ParameterGroup) generalParameterValue;
                DefaultParameterDescriptorGroup groupDesc = (DefaultParameterDescriptorGroup) generalParameterValue.getDescriptor();

                DefaultParameterDescriptorGroup layerGroup = (DefaultParameterDescriptorGroup) paramDescriptor;
                
                List layerDescriptors = layerGroup.descriptors();
                GeneralParameterValue[] layerParameterValues = new GeneralParameterValue[layerDescriptors.size()];

                
                for (int j = 0; j < layerDescriptors.size(); j++) 
                {
                    GeneralParameterDescriptor layerDescriptor = (GeneralParameterDescriptor) layerDescriptors.get(j);
                    //System.out.println(layerDescriptor.getName().getCode());
                    
                    if(layerDescriptor.getName().getCode().equals("Bathymetry")||
                            layerDescriptor.getName().getCode().equals("Countries")||
                            layerDescriptor.getName().getCode().equals("DOQ")||
                            layerDescriptor.getName().getCode().equals("UrbanArea"))
                    {
                        Parameter layerValue = (Parameter) layerDescriptor.createValue();
                        layerParameterValues[j] = layerValue;

                        ParameterDescriptor layerDesc = (ParameterDescriptor) layerValue.getDescriptor();
                        //System.out.println("LayerDesc:"+layerDesc.toString());
                        Set styles = layerDesc.getValidValues();
                        //System.out.println("styles:"+styles.toString());
                        //layerValue.setValue(styles.iterator().next());
                        //layerValue = new Parameter("DOQ",0);
                        layerValue.setValue(null);
                        //System.out.println("layerValue:"+layerValue.toString());

                        groupValue.values().add(layerValue);
                    }
                    //break;
                }
                
                continue;
            }
            
            Parameter value = (Parameter) generalParameterValue;
            ParameterDescriptor desc = (ParameterDescriptor) generalParameterValue.getDescriptor();

            if (parameterName.equals("FORMAT")) 
            {
                Iterator iter = desc.getValidValues().iterator();

                if (iter.hasNext()) {
                    String format = (String) iter.next();
                    //System.out.println("Format:"+format);
                    value.setValue(format);
                }

                continue;
            }

            if (parameterName.equals("WIDTH") || parameterName.equals("HEIGHT")) 
            {
                value.setValue(400);

                continue;
            }

            if (parameterName.equals("SRS")) {
                //value.setValue("EPSG:4326");
                value.setValue("EPSG:26910");
                continue;
            }

            if (parameterName.equals("BBOX_MINX")) 
            {
                //value.setValue(-168.67);
                value.setValue(524800.00);
                //value.setValue(-131.13151509433965);
                //request.setBBox("-131.13151509433965,46.60532747661736,-117.61620566037737,56.34191403281659");
        
                continue;
            }

            if (parameterName.equals("BBOX_MINY")) {
                //value.setValue(17.84);
                value.setValue(4168400.00);
                //value.setValue(46.60532747661736);
                //request.setBBox("-131.13151509433965,46.60532747661736,-117.61620566037737,56.34191403281659");
        
                continue;
            }

            if (parameterName.equals("BBOX_MAXX")) {
                //value.setValue(-65.15);
                value.setValue(576000.00);
                //value.setValue(-117.61620566037737);
                continue;
            }

            if (parameterName.equals("BBOX_MAXY")) {
                //value.setValue(71.55);
                value.setValue(4200400.00);
                //value.setValue(56.34191403281659);
                continue;
            }

            if (parameterName.equals("VERSION")) {
                value.setValue("1.1.1");

                continue;
            }
        }
        
        try
        {
            GridCoverage2D coverage = (GridCoverage2D)reader.read(generalParameterValues);
            StyleBuilder sb = new StyleBuilder();
            RasterSymbolizer rsDem = sb.createRasterSymbolizer();
            Style demStyle = sb.createStyle(rsDem);
            //System.out.println("created Style");    

            MapLayer m0 = new DefaultMapLayer(coverage,demStyle);
            viewerGUI.context.addLayer(m0);
        }
        catch(Exception e)
        {
            System.out.println("Issues with WMDREader:"+e.toString());
        }
    }
    
    public void loadWMSInputStream() throws Exception
    {
        URL url = null;
        try 
        {
          //url = new URL("http://www2.dmsolutions.ca/cgi-bin/mswms_gmap?VERSION=1.1.0&REQUEST=GetCapabilities");
          //url = new URL("http://terraservice.net/ogccapabilities.ashx?version=1.1.1&request=GetCapabilties");
          url = new URL("http://www2.demis.nl/mapserver/Request.asp?Service=WMS&Version=1.3.0&Request=GetCapabilities");
           
        } 
        catch (MalformedURLException e) {
          //will not happen
        }

        WebMapServer wms = null;
        try 
        {
          wms = new WebMapServer(url);
        } 
        catch (IOException e) 
        {
          //There was an error communicating with the server
          //For example, the server is down
          System.out.println("IOException:"+e.toString());
        } 
        catch (ServiceException e) 
        {
          //The server returned a ServiceException (unusual in this case)
          System.out.println("ServiceException:"+e.toString());
        } 
        catch (SAXException e) 
        {
          //Unable to parse the response from the server
          //For example, the capabilities it returned was not valid
          System.out.println("SAXException:"+e.toString());
        }
        
        //System.out.println("made WebMapServer Connect");
        
        WMSCapabilities capabilities = wms.getCapabilities();

        String serverName = capabilities.getService().getName();
        String serverTitle = capabilities.getService().getTitle();
        //System.out.println("Capabilities retrieved from server: " + serverName + " (" + serverTitle + ")");

        if (capabilities.getRequest().getGetFeatureInfo() != null) 
        {
            //This server supports GetFeatureInfo requests! We could make one if we wanted to.
        }

        //gets the top most layer, which will contain all the others
        Layer rootLayer = capabilities.getLayer();

        //gets all the layers in a flat list, in the order they appear in
        //the capabilities document (so the rootLayer is at index 0)
        //List layers = capabilities.getLayerList();
        
        Layer[] layers = WMSUtils.getNamedLayers(capabilities);
        Layer layer = null;
        
        
        for(int i =0; i< layers.length; i++)
        {
            Layer test = (Layer) layers[i];
            //System.out.println("layer:"+test.getName());
            
            if( test.getName() != null && test.getName().length() != 0 )
            {
                //System.out.println("layers:"+layer.getName());
                if(test.getName().equals("Bathymetry"))
                {
                    layer = test;
                    //break;
                }
            }
        }
        
        try
        {
        //CoordinateReferenceSystem crs = CRS.decode("EPSG:4326");
        CoordinateReferenceSystem crs = DefaultGeographicCRS.WGS84;
        GeneralEnvelope envelope = wms.getEnvelope(layer, crs);
        
        System.out.println("Min0:"+envelope.getMinimum(0));
        System.out.println("Min1:"+envelope.getMinimum(1));
        System.out.println("Max0"+envelope.getMaximum(0));
        System.out.println("Max1:"+envelope.getMaximum(1));
        
        
        }
        catch(Exception exc)
        {
            System.out.println("Error getting map boundries"+exc.toString());
        }
        
        GetMapRequest request = wms.createGetMapRequest();
        request.setFormat("image/png");
        request.setDimensions("400", "400"); //sets the dimensions of the image to be returned from the server
        request.setTransparent(true);
        request.setSRS("EPSG:4326");
        //request.setBBox("-170.0,30.0,-10.0,80.0");
        request.setBBox("-131.13151509433965,46.60532747661736,-117.61620566037737,56.34191403281659");
        request.addLayer(layer);
        System.out.println(request.getFinalURL());
		
        
        System.out.println("before issuin image request");
        BufferedImage image = null;
        
        try
        {
            GetMapResponse response = wms.issueRequest( request );
            
            System.out.println(response.getContentType());
            
            System.out.println("before getting inputstream");
                
            InputStream is = response.getInputStream();

            System.out.println("before readin image");

            image = ImageIO.read(is);
        }
        catch(Exception e)
        {
            System.out.println("issue getting wms request:"+e.toString());
        }
        
        String s =  "wms image";
        MapLayer m0 = null;
           
        System.out.println("before flippin wms images");
        
        try
        {
            GridCoverageFactory gcf = new GridCoverageFactory();
            GridCoverage2D coverage = gcf.create((CharSequence)s, image.getRaster(), envlp_NoEdges2);
            StyleBuilder sb = new StyleBuilder();
            RasterSymbolizer rsDem = sb.createRasterSymbolizer();
            Style demStyle = sb.createStyle(rsDem);
            
            m0 = new DefaultMapLayer(coverage,demStyle);
            viewerGUI.context.addLayer(m0);
        }
        catch(Exception ex)
        {
            System.out.println("wms error:"+ex.toString());
        }
        
        /*
        GraphicStyle2D gs = new GraphicStyle2D(image,0,1);
        MapLayer m0 = null;// = new DefaultMapLayer();
        m0.setStyle((Style)gs);
        */
        
        
        System.out.println("added wms image");
        
    }
    
    public MapLayer loadWorldImage(String resourcePath) throws Exception
    {
        
        System.out.println("wireader");
        
        File file = null;
        URL url = null;
        AbstractGridFormat format = null;
        MapLayer m0 = null;
        
        
        try
        {
            /* Add geotiffs to the map. */
            File f = new File(resourcePath);
            //url = new URL(resourcePath);
            //url = this.viewerData.getClass().getResource(resourcePath);
            url = f.toURL();
            //url = this.getClass().getResource("../data/US_UrbanAreas/houston.jpg");
            //url = this.getClass().getResource("../data/US_UrbanAreas/usa.tif");
            //url = this.getClass().getResource("../data/US_UrbanAreas/Pk50095.tif");
            //url = this.getClass().getResource("../data/61069071/61069071.tif");
            System.out.println(url.toString());
            
            format = new org.geotools.gce.image.WorldImageFormat();
        
        }
        catch(Exception e)
        {
            System.out.println("Probelm loading world image:"+e.toString());
        }
        
        GridCoverageReader reader = null;
        GridCoverage2D coverage = null;
     
        //System.out.println("got format ok");
        
         //loop through background images..
        try
        {
            if(format.accepts(url))
            {
                //System.out.println("about to create a world image reader");
                // getting a reader
                
                reader = new org.geotools.gce.image.WorldImageReader(url);
                
                
                if(reader!=null)
                {
                    //System.out.println("about to read coverage");
                    // reading the coverage
                    coverage = (GridCoverage2D) reader.read(null);

                    //System.out.println("read coverage");
                    StyleBuilder sb = new StyleBuilder(); 

                    //RasterSymbolizer rsDem = sb.createRasterSymbolizer(cm,1);
                    RasterSymbolizer rsDem = sb.createRasterSymbolizer();
                    Style demStyle = sb.createStyle(rsDem);
                    //System.out.println("created Style");    
                    
                    //viewerGUI.jmp.setMapArea(envlp_NoEdges3);
                    //coverage.geophysics(false).show();

                    // Add geotiff to map,...
                    m0 = new DefaultMapLayer(coverage,demStyle);
                    //viewerGUI.context.addLayer(m0);

                    //viewerGUI.context.addLayer((org.geotools.gce.geotiff.GeoTiffReader)reader,demStyle);
                    
                    this.CurrentEnvelope = coverage.getEnvelope();
                
                }
            }
            else
            {
                System.out.println("url not accepted");
            }
        }
        catch(Exception e)
        {
            System.out.println("Error adding world image layer to Map:"+e.toString());
            e.printStackTrace();
        }
        
        return m0;
    }
    
    
    public void loadGeoTiffImage() throws Exception
    {
        File file = null;
        URL url = null;
        AbstractGridFormat format = null;
        
        try
        {
            /* Add geotiffs to the map. */
            url = this.getClass().getResource("../data/22870176/22870176.tif");
            format = new org.geotools.gce.geotiff.GeoTiffFormat();
        
        }
        catch(Exception e)
        {
            System.out.println("Probelm loading geoTiff:"+e.toString());
        }
        
        GridCoverageReader reader = null;
        GridCoverage2D coverage = null;
        
        //loop through background images..
        try
        {
            if(format.accepts(url))
            {
                //System.out.println("about to create a reader");
                // getting a reader
                
                reader = new org.geotools.gce.geotiff.GeoTiffReader(url, new Hints(
                            Hints.FORCE_LONGITUDE_FIRST_AXIS_ORDER, Boolean.TRUE));
                
                //reader = new org.geotools.gce.geotiff.GeoTiffReader(url);

                if(reader!=null)
                {
                    System.out.println("about to read coverage");
                    // reading the coverage
                    coverage = (GridCoverage2D) reader.read(null);

                    System.out.println("read coverage");
                    StyleBuilder sb = new StyleBuilder(); 

                    //RasterSymbolizer rsDem = sb.createRasterSymbolizer(cm,1);
                    RasterSymbolizer rsDem = sb.createRasterSymbolizer();
                    Style demStyle = sb.createStyle(rsDem);
                    System.out.println("created Style");    
                    
                    envlp_NoEdges3 = new ReferencedEnvelope(((org.geotools.gce.geotiff.GeoTiffReader)reader).getOriginalEnvelope(), 
                    ((org.geotools.gce.geotiff.GeoTiffReader)reader).getCrs());



                    //viewerGUI.jmp.setMapArea(envlp_NoEdges3);
                    //coverage.geophysics(false).show();

                    // Add geotiff to map,...
                    MapLayer m0 = new DefaultMapLayer(coverage,demStyle);
                    viewerGUI.context.addLayer(m0);

                    //viewerGUI.context.addLayer((org.geotools.gce.geotiff.GeoTiffReader)reader,demStyle);
                }
            }
        }
        catch(Exception e)
        {
            System.out.println("Error adding geotiff layer to Map:"+e.toString());
        }
    }
    
    public void removeActiveLayers()
    {
        if(this.viewerData.currentActiveLayers.length >0)
        {
            this.viewerGUI.context.removeLayers(this.viewerData.currentActiveLayers);
        }
    }
    
    public void init(){
        
        try
        {
            ViewerManager vb = new ViewerManager();

            vb.viewerGUI  = new Viewer(vb); 
            
            vb.LoadDefaults();
        }
        catch(Exception e)
        {
            System.out.println("MainProblems:"+e.toString());
            e.printStackTrace();
        }
   }

  public void start(){
     System.out.println("Applet starting.");
  }

  public void stop(){
     System.out.println("Applet stopping.");
  }

  public void destroy(){
     System.out.println("Destroy method called.");
  }

   public void actionPerformed(ActionEvent event){
        Object source = event.getSource();
   }
   
    public static void main(String[] args) 
    {
        
        try
        {
            ViewerManager vb = new ViewerManager();

            vb.viewerGUI  = new Viewer(vb); 
            
            vb.LoadDefaults();
        }
        catch(Exception e)
        {
            System.out.println("MainProblems:"+e.toString());
            e.printStackTrace();
        }
    }
    
    private void loadCountryShapesAndStyles()
    {
        try
        {
            /* Add the Shapefile FeatureSource below the first layer. */
            FeatureSource shpFS = getAShapefileFeatureSourceFromCatalog(SHAPEFILENAME);
            Style shpsty = (Style) viewerData.theStyleMap.get("shpstyl");
            MapLayer m1 = new DefaultMapLayer(shpFS,shpsty);
            viewerGUI.context.addLayer(m1);
        
        }
        catch(Exception e)
        {
            System.out.println("ERROR[loadCountryShapesAndStyles]:"+e.toString());
            e.printStackTrace();
        }
    }
    
    /*
     * Method loads boundries for Texas, styles, imagery and displays them on a map.
     * The map is an ortho urban area of the region at 1 meter.
     */
    private void loadHoustonInfo()
    {
        this.viewerData.currentActiveLayers = new MapLayer[3];    
        selectedAerea = UrbanArea.HOUSTON;
        viewerGUI.jmp.setVisible(false);
        
        try
        {
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // TX State shapefiles
            FeatureSource shpFS5 = getAShapefileFeatureSourceFromCatalog(SHAPEFILENAME5);
            Style shpsty5 = (Style) viewerData.theStyleMap.get("shpstyl5");
            MapLayer m5 = new DefaultMapLayer(shpFS5,shpsty5);
            this.viewerData.currentActiveLayers[0] = m5;
            
            // Houston Area Map
            //this.viewerData.currentActiveLayers[1] = loadWorldImage("US_UrbanAreas/houston.jpg");
            this.viewerData.currentActiveLayers[1] = loadWorldImage(this.URBANAREADIR + "/houston.jpg");
                    
            // TX zip codes
            FeatureSource shpTXZipFS = getAShapefileFeatureSourceFromCatalog(TX5DIGIT);
            Style shpTXZipSty = (Style) viewerData.theStyleMap.get("txZipStyles");
            MapLayer m8 = new DefaultMapLayer(shpTXZipFS,shpTXZipSty);
            this.viewerData.currentActiveLayers[2] = m8;
            
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // Add Layers
            viewerGUI.context.addLayers(this.viewerData.currentActiveLayers);
          
            // Set zoom factor
            viewerGUI.jmp.setZoomFactor(36.0);
            
            //Zoom In
            viewerGUI.jmp.AutomatedAction(250,121,JMapPane.ZoomIn);
             
            // Reset zoom factor
            viewerGUI.jmp.setZoomFactor(10.0);
            
            //Zoom In
            viewerGUI.jmp.AutomatedAction(338,170,JMapPane.ZoomIn);
            
            addDemographicLayer(0,0);
            
            viewerGUI.jmp.setVisible(true);
        }
        catch(Exception e)
        {
            System.out.println("ERROR[loadHoustonInfo]"+e.toString());
            e.printStackTrace();
        }
    }
    
    /*
     * Method loads boundries for IL, styles, imagery and displays them on a map.
     * The map is an ortho urban area of the region at 1 meter.
     */
    private void loadChicagoInfo()
    {
        this.viewerData.currentActiveLayers = new MapLayer[3];
        
        try
        {
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // IL State shapefiles
            FeatureSource shpFS3 = getAShapefileFeatureSourceFromCatalog(SHAPEFILENAME3);
            Style shpsty3 = (Style) viewerData.theStyleMap.get("shpstyl3");
            MapLayer m3 = new DefaultMapLayer(shpFS3,shpsty3);
            //viewerGUI.context.addLayer(m3);
            this.viewerData.currentActiveLayers[0] = m3;
            
            // Chicago Area Map
            this.viewerData.currentActiveLayers[1] = loadWorldImage(this.URBANAREADIR + "/chicago.jpg");
            
            // IL Zip codes
            FeatureSource shpILZipFS = getAShapefileFeatureSourceFromCatalog(IL5DIGIT);
            Style shpILZipSty = (Style) viewerData.theStyleMap.get("ilZipStyles");
            MapLayer m7 = new DefaultMapLayer(shpILZipFS,shpILZipSty);
            this.viewerData.currentActiveLayers[2] = m7;
            
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // Add Layers
            viewerGUI.context.addLayers(this.viewerData.currentActiveLayers);
          
            // Set zoom factor
            viewerGUI.jmp.setZoomFactor(36.0);
             
            //Zoom In
            viewerGUI.jmp.AutomatedAction(260,120,JMapPane.ZoomIn);
            
        }
        catch(Exception e)
        {
            System.out.println("ERROR[loadChicagoInfo]"+e.toString());
            e.printStackTrace();
        }    
    }
   
    /*
     * Method loads boundries for IL, styles, imagery and displays them on a map.
     * The map is an ortho urban area of the region at 1 meter.
     */
    private void loadLemontInfo()
    {
        this.viewerData.currentActiveLayers = new MapLayer[3];
        
        try
        {
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // IL State shapefiles
            FeatureSource shpFS3 = getAShapefileFeatureSourceFromCatalog(SHAPEFILENAME3);
            Style shpsty3 = (Style) viewerData.theStyleMap.get("shpstyl3");
            MapLayer m3 = new DefaultMapLayer(shpFS3,shpsty3);
            //viewerGUI.context.addLayer(m3);
            this.viewerData.currentActiveLayers[0] = m3;
            
            // Lemont Area Map
            this.viewerData.currentActiveLayers[1] = loadWorldImage(this.URBANAREADIR + "/lemont.jpg");
            
            // IL Zip codes
            FeatureSource shpILZipFS = getAShapefileFeatureSourceFromCatalog(IL5DIGIT);
            Style shpILZipSty = (Style) viewerData.theStyleMap.get("ilZipStyles");
            MapLayer m7 = new DefaultMapLayer(shpILZipFS,shpILZipSty);
            this.viewerData.currentActiveLayers[2] = m7;
            
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // Add Layers
            viewerGUI.context.addLayers(this.viewerData.currentActiveLayers);
          
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // Set zoom factor
            viewerGUI.jmp.setZoomFactor(36.0);
             
            //Zoom In
            viewerGUI.jmp.AutomatedAction(260,120,JMapPane.ZoomIn);
        }
        catch(Exception e)
        {
            System.out.println("ERROR[loadChicagoInfo]"+e.toString());
            e.printStackTrace();
        }
    
    }
     /*
     * Method loads boundries for DC, styles, imagery and displays them on a map.
     * The map is an ortho urban area of the region at 1 meter.
     */
    private void loadDCInfo()
    {
        this.viewerData.currentActiveLayers = new MapLayer[3];
                
        try
        {
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            

            // MD State shapefiles
            FeatureSource shpFS4 = getAShapefileFeatureSourceFromCatalog(SHAPEFILENAME4);
            Style shpsty4 = (Style) viewerData.theStyleMap.get("shpstyl4");
            MapLayer m4 = new DefaultMapLayer(shpFS4,shpsty4);
            //viewerGUI.context.addLayer(m4);
            this.viewerData.currentActiveLayers[0]=m4;
            
            // DC Area Map
            this.viewerData.currentActiveLayers[1] = loadWorldImage(this.URBANAREADIR + "/washington_dc.jpg");
        
            // District Zip codes
            FeatureSource shpDCZipFS = getAShapefileFeatureSourceFromCatalog(DC5DIGIT);
            Style shpDCZipSty = (Style) viewerData.theStyleMap.get("dcZipStyles");
            MapLayer m6 = new DefaultMapLayer(shpDCZipFS,shpDCZipSty);
            this.viewerData.currentActiveLayers[2] = m6;
            
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // Add Layers
            viewerGUI.context.addLayers(this.viewerData.currentActiveLayers);
            
            // Set zoom factor
            viewerGUI.jmp.setZoomFactor(36.0);
            
            //Zoom In
            viewerGUI.jmp.AutomatedAction(260,120,JMapPane.ZoomIn);
           
        }
        catch(Exception e)
        {
            System.out.println("ERROR[loadDCInfo]:"+e.toString());
            e.printStackTrace();
        }
    }
    
    /*
     * Method loads boundries for Texas, styles, imagery and displays them on a map.
     * The map is an ortho urban area of the region at 1 meter.
     */
    private void loadSanFranciscoInfo()
    {
        this.viewerData.currentActiveLayers = new MapLayer[2];    
        selectedAerea = UrbanArea.SANFRAN;
        viewerGUI.jmp.setVisible(false);
        
        try
        {
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // San Fran Area Map
            //this.viewerData.currentActiveLayers[1] = loadWorldImage("US_UrbanAreas/houston.jpg");
            this.viewerData.currentActiveLayers[0] = loadWorldImage(this.URBANAREADIR + "/sanFrancisco.jpg");
                    
            // TX zip codes
            FeatureSource shpTXZipFS = getAShapefileFeatureSourceFromCatalog(CA5DIGIT);
            Style shpTXZipSty = (Style) viewerData.theStyleMap.get("caZipStyles");
            MapLayer m8 = new DefaultMapLayer(shpTXZipFS,shpTXZipSty);
            this.viewerData.currentActiveLayers[1] = m8;
            
            // Set original zoom factor
            viewerGUI.jmp.setZoomFactor(2.0);
        
            // Set map Area
            viewerGUI.jmp.setMapArea(this.NOEDGES);
            
            // Reset Map
            viewerGUI.jmp.AutomatedAction(35,0,JMapPane.Reset);
            
            // Add Layers
            viewerGUI.context.addLayers(this.viewerData.currentActiveLayers);
            
            // Set zoom factor
            viewerGUI.jmp.setZoomFactor(36.0);
            
            //Zoom In
            viewerGUI.jmp.AutomatedAction(154,96,JMapPane.ZoomIn);
            
            //Zoom In
            //viewerGUI.jmp.AutomatedAction(440,240,JMapPane.ZoomIn);
            
            // Reset zoom factor
            viewerGUI.jmp.setZoomFactor(10.0);
            
            //Zoom In
            viewerGUI.jmp.AutomatedAction(440,240,JMapPane.ZoomIn);
            
            // Reset zoom factor
            viewerGUI.jmp.setZoomFactor(3.5);
            
            viewerGUI.jmp.AutomatedAction(545,240,JMapPane.ZoomIn);
            
            
            addDemographicLayer(0,0);
            
            viewerGUI.jmp.setVisible(true);
        }
        catch(Exception e)
        {
            System.out.println("ERROR[loadSanFranInfo]"+e.toString());
            e.printStackTrace();
        }
    }
}

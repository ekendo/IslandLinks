package org.islandlinks.Viewer.GIS.point.repositories 
{
	// AS3 SDK
	import flash.utils.*;
	
	// flex sdk libs
	import mx.events.*;
	import mx.collections.ArrayCollection;
	import mx.utils.ObjectUtil;
	import mx.rpc.events.*
	import mx.rpc.soap.*;
	import mx.rpc.http.*;
	import mx.rpc.xml.*;
	
	// airliquide sdk
	import org.islandlinks.Viewer.GIS.map.Feature;
	import org.islandlinks.Viewer.GIS.map.Filter;
	import org.islandlinks.Viewer.GIS.map.WMSLayer;
	import org.islandlinks.Viewer.GIS.map.manager.*;
	import org.islandlinks.Viewer.GIS.client.UiButton;
	import org.islandlinks.Viewer.GIS.client.navigation.FeatureManager;
	import org.islandlinks.Viewer.GIS.client.navigation.FilterManager;
	
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class ArcGisSvr
	{
		// gis server source types
		public static const WFS:int = 1;
		public static const WMS:int = 2;
		public static const WCS:int = 3;
		public static const KML:int = 4;
		public static const GML:int = 5;
		public static const MAP:int = 6;
		public static const MDA:int = 7;
		
		public var Caller:Object;
		
		private var httpLocation:HTTPService;
		
		private var fType:Number;
		private var svrSource:Number;
		private var svrUrl:String;
		private var attributeData:ArrayCollection;
		
		public function ArcGisSvr(url:String):void 
		{
			this.svrUrl = url;
			this.httpLocation = new HTTPService(url);
		}
		
		/* Gets */
		public function GetFeatureAttributeData(featureType:Number):void 
		{
			
			// set type
			this.fType = featureType;
			
			// build request
			switch(featureType)
			{
				
			}
			
			
		}
		
		public function GetAttributeData():ArrayCollection
		{
			return this.attributeData;
		}
		
		public function GetHttpService():HTTPService
		{
			return httpLocation;
		}
		
		/* Sets */
		public function SetServerSource(source:int):void
		{
			this.svrSource = new Number(source);
		}
		
		/* helpers */
		private function wfsDecoder (myXML:XML):void
		{
			 
		}
		
		/* events */
		private function wfsDataResponse(event:ResultEvent):void
		{
			//this.attributeData = event.result.FeatureCollection as ArrayCollection;
		}
		
		public function sendRequest():void
		{
			this.httpLocation.send();
		}
	}

}
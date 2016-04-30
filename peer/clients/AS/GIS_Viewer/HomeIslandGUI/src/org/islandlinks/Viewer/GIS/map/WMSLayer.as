package org.islandlinks.Viewer.GIS.map 
{
	import com.esri.ags.SpatialReference;
	import com.esri.ags.Units;
	import com.esri.ags.geometry.Extent;
	import com.esri.ags.layers.DynamicMapServiceLayer;
	import com.esri.ags.events.LayerEvent;
		
	import flash.display.Loader;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	
	import mx.controls.*;

	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class WMSLayer extends DynamicMapServiceLayer
	{
		private var _params:URLVariables;
		private var _urlRequest:URLRequest;
		private var _url:String;
		private var _debug:String;
		
		public function WMSLayer(val:String, lVal:String, sVal:String) 
		{
			super();
			
			setLoaded(true);
			
			// init constant parameter values
			_params = new URLVariables();
			_params.request = "GetMap";
			_params.transparent = true;
			_params.format = "image/png";
			_params.version = "1.1.1";
			_params.layers = lVal;  
			_params.styles = sVal;  // each layer needs a matching style
			
			_urlRequest = new URLRequest(val);
			_urlRequest.data = _params; // set params on URLRequest object
			
			// set up layer load handler
			addEventListener(LayerEvent.LOAD_ERROR, layerErrorHandler);
		}
		
		public function set url(val:String):void
		{
			this._url = val;
			_urlRequest = new URLRequest(val);
			_urlRequest.data = _params; // set params on URLRequest object
		}
		
		public function get url():String
		{
			return this._url;
		}
		
		public function set debug(val:String):void
		{
			this._debug = val;
		}
		
		public function get debug():String
		{
			return this._debug;
		}
		
		override public function get initialExtent():Extent
		{
			return new Extent(-165, 18, -67, 67, new SpatialReference(4326));
		}

		override public function get spatialReference():SpatialReference
		{
			return new SpatialReference(4326);
		}

		override public function get units():String
		{
			return Units.DECIMAL_DEGREES;
		}
    
		//--------------------------------------------------------------------------
		//
		//  Overridden methods
		//      loadMapImage(loader:Loader):void
		//
		//--------------------------------------------------------------------------
		
		override protected function loadMapImage(loader:Loader):void
		{
			// update changing values
			_params.bbox = map.extent.xmin + "," + map.extent.ymin + "," + map.extent.xmax + "," + map.extent.ymax;
			_params.srs = "EPSG:41001";// + map.spatialReference.wkid,
			_params.width = map.width;
			_params.height = map.height;
			//Alert.show(_params.bbox);
			loader.load(_urlRequest);
		}
		
		private function layerErrorHandler(e:LayerEvent):void
		{
			this._debug = "something went wrong here";

		}
	}

}
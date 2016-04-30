package com.airliquide.alps.client.manager 
{
	// as3 sdk libs
	import flash.events.*;
	import flash.display.Sprite;
	import flash.text.TextField;
	import flash.utils.*;
	
	
	// adobe air sdk libs
	//import flash.filesystem.File;
	
	// esri sdk libs
	import com.esri.ags.Map;
	
	// flex sdk libs
	import mx.events.*;
	import mx.controls.*;
	import mx.containers.*
	import mx.core.Container;
	import mx.core.UIComponent;
	
	// airliquide sdk
	import com.airliquide.alps.client.SubWindow;
	import com.airliquide.alps.client.manager.UiButton;
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class ClientUi extends Canvas
	{
		
		public function ClientUi() 
		{
			super();
		}
		
		public function infActive(e:Event):void
		{
    		//this.viewPoint = "inf";
    		
			//if (this.selectedSeries != null)
			{
    		
			}
    	}
    		
    	public function inspActive(e:Event):void
		{
    		//this.viewPoint = "ins";
    		/*
			if (this.selectedSeries != null)
			{
    			//this.loadCPReadingDates(this.selectedSeries.label);
    			//cpMgm.selectedChild = cpReportPane;
    		}
    		else 
			{
    			//cpMgm.selectedChild = CPDefault;
    		}
			*/
    	}
		
	}

}
package com.airliquide.alps.report
{
	import mx.collections.ArrayCollection;
	import mx.controls.Alert;
	
	public class CPReport
	{
		
		public var rType:String;
		public var range:String;
		public var guid:String;
		public var from:String;
		public var readings:ArrayCollection;
		
		public function CPReport():void
		{
			this.readings = new ArrayCollection();
			//Alert.show( "Constructor readings " + this.readings);
		}
		
		public function hasStationPoint(id:Number):int{
			//Alert.show( "readings " + this.readings);
		
			for (var i:int = 0; i < this.readings.length; i++){
				//Alert.show("loop");
				if (this.readings[i].id == id){
					return i;
				}
			}
			
			return -1;
		}
		
	}
}
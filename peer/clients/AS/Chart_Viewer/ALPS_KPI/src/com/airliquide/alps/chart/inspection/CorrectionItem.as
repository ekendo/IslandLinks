package com.airliquide.alps.inspection
{
	public class CorrectionItem
	{
		public static const POTENTIAL:String = "POTENTIAL";
		public static const VOLTAGE:String = "VOLTAGE";
		public static const RESISTANCE:String = "RESISTANCE";
		public static const COMMENT:String = "COMMENT";
		public static const P2S:String = "P2S";
		public static const CASING:String = "CASING";
		public static const BOND:String = "BOND";
		
		public var eventID:int = 0;
		public var oldValue:String = "";
		public var newValue:String = "";
		public var valueType:String = "";
		public var readingType:String = "";
		
		public function CorrectionItem(eid:int, ov:String, nv:String, vt:String, rt:String){
			this.eventID = eid;
			this.oldValue = ov;
			this.newValue = nv;
			this.valueType = vt;
			this.readingType = rt;
		}
		
		public function toString():String{
			return eventID + " " + this.oldValue + " " + this.newValue + " " + this.valueType + " " + this.readingType;
		}
	}
}
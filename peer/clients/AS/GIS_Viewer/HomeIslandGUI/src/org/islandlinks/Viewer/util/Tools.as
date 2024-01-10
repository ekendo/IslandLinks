package org.islandlinks.Viewer.util
{
	public class Tools
	{
		public static function xml2Date(s:String):Date{
				var s2:Array = s.split("T");
				var date:Array = s2[0].split("-");
				var time:Array = s2[1].split(":");
				return (new Date(Number(date[0]), Number(date[1]) - 1, Number(date[2]), Number(time[0]), Number(time[1]), Number(time[2])));
			}
			
			public  static function formatDate(d:Date):String{
				return (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
			}
    		
    		public  static function getRandomNumber():Number{
    			return Math.round(Math.random() * 100);
    		}
    		
    		public  static function parseDateString(s:String):String{
    			var s1:Array = s.split("T");
    			return String(s1[0]);
    		}
    		
    		public static function isOutOfBoundValue(low:Number, high:Number, target:Number):Boolean{
    			if (target == 0){
    				return false;
    			}
    			//Alert.show(low + "/" + (low * -1) + " " + high + "/" + (high * -1) + " " + target);
    			if (target >= 0){
	    			if (low >= target || high <= target){
	    				return true;
	    			}
    			}
    			else {
	    			if ((low * -1) <= target || (high * -1) >= target){
	    				return true;
	    			}
    			}
    			return false;
    		}
	}
}
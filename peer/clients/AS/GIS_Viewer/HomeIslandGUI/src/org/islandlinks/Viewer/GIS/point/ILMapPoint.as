package org.islandlinks.Viewer.GIS.point
{
	import flash.display.MovieClip;
	import flash.text.TextField;
	import flash.text.TextFormat;
	
	public class ALMapPoint extends MovieClip
	{
		public static const WHITE:Number = 0xFFFFFF;
		public static const YELLOW:Number = 0xFFFF00;
		public static const RED:Number = 0xFF0000;
		public static const LIGHT2_BLUE:Number = 0x00FFFF;
		public static const LIGHT1_BLUE:Number = 0xFF00FF;
		public static const BLUE:Number = 0x0000FF;
		public static const BLACK:Number = 0x000000;
		public static const GRAY:Number = 0xe7e7e7;
		
		public var point:StationPoint;
		
		
		public function ALMapPoint(point:StationPoint)
		{
			this.point = point;
			this.draw();
		}
		
		public function draw():void
		{
			var c:Number = ALMapPoint.GRAY;
			switch (this.point.product)
			{
				case "H" :
					c = ALMapPoint.WHITE;
					break;
				case "N" :
					c = ALMapPoint.BLACK;
					break;
				case "O2" :
					c = ALMapPoint.BLUE;
					break;
			}
			
			var t:String= "T";
			switch (this.point.installations.getItemAt(0).type)
			{
					case 0:
						t = "V";
						break;
					case 1:
						t = "T";
						break;
					case 2:
						t = "B";
						break;
					case 3:
						t = "C";
						break;
					case 4:
						t = "M";
						break;
					case 5:
						t = "X";
						break;
					case 6:
						t = "R";
						break;
					case 7:
						t = "S";
						break;
				}
			
			
			this.graphics.beginFill(c, 0.5);
			this.graphics.drawCircle(0,0,8);
			this.graphics.endFill();
			
			this.graphics.beginFill(ALMapPoint.BLACK, 0.5);
			this.graphics.drawCircle(0,0,7);
			this.graphics.endFill();
			
			this.graphics.beginFill(ALMapPoint.WHITE, 0.8);
			this.graphics.drawCircle(0,0,6);
			this.graphics.endFill();
			
			var tf:TextField = new TextField();
			tf.selectable = false;
			
			var format:TextFormat = tf.defaultTextFormat;
			format.align = "center";
			format.size = 9;
			format.bold = true;
			tf.defaultTextFormat = format;
			
			tf.text = t;
			
			tf.height = 12;
			tf.width = 12;
			tf.x = -6;
			tf.y = -6;
			
			this.addChild(tf);
			
		}
	}
}
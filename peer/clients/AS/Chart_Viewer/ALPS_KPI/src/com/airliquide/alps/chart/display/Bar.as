package com.airliquide.alps.chart.display
{
	// Yahoo SDK
	//import com.yahoo.astra.fl.charts.BarChart;
	//import com.yahoo.astra.fl.charts.series.Series;
	
	// AM SDK
	import com.amcharts.AmSerialChart;
	import com.amcharts.chartClasses.*;
	
	// FLEX SDK
	import mx.charts.*;
	import mx.charts.chartClasses.CartesianChart;
	import mx.charts.chartClasses.IAxis;
	import mx.charts.series.*;
	import mx.controls.Label;
	import mx.controls.Alert;
	
	// Airliquide SDK
	import com.airliquide.alps.chart.*;
	
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class Bar extends ChartDisplayFormat
	{
		
		private var xAxisLen:int = 0;
		private var yAxisLen:int = 0;
		
		private var verticalCategoryAxis:CategoryAxis;
		private var horizontalValueAxis:String;
		
		private var flexData:BarSeries;
		private var fbChart:mx.charts.BarChart;
		private var abCharts:AmSerialChart
		private var abChart:AmGraph;
		
		private var series:Array;
		private var strokes:Array;
		private var colors:Array;
		
		private var useBarSeries:Boolean;
		
		public var dbgText:Label;
		
		public function Bar() 
		{
			super();
			
			this.xAxisLen = 100;
			this.yAxisLen = 100;
			
			dbgText = new Label();
		}
		
		public function SetXAxis(x:int):void
		{
			this.xAxisLen = x;
		}
		
		public function SetYAxis(y:int):void
		{
			this.yAxisLen = y;
		}
		
		public function SetYAxisCategory(yca:CategoryAxis):void
		{
			this.verticalCategoryAxis = yca;
		}
		
		public function SetXAxisValueField(xvf:String):void
		{
			this.horizontalValueAxis = xvf;
		}
		
		public function SetStrokes(s:Array):void
		{
			this.strokes = s;
		}
		
		public function SetColors(c:Array):void
		{
			this.colors = c;
		}
		
		public function SetSeries(s:Array):void
		{
			this.series = s;
		}
		
		public function DrawBarChart():void
		{
			//dbgText.text += "In DrawBarChart";
			
			switch(this.RenderEngine)
			{
				case ALChart.FLEX:
					//dbgText.text += "it is a FLEX chart:";
					
					fbChart = new BarChart();
					
					// add vertical Axis
					this.fbChart.verticalAxis = this.verticalCategoryAxis;
					
					// add bar series
					this.fbChart.series = this.series;
					
					// set provider
					fbChart.dataProvider = this.GetArrayCollectionData();
					break;
				case ALChart.AM:
					abChart = new AmGraph();
					
					this.abChart.type = "column";
					
					this.abChart.valueField = horizontalValueAxis;
					
					this.abChart.dataProvider = this.GetArrayCollectionData();
					//Alert.show("returning abchart");
					break;
			}
		}
		
		
		public function GetChart():Object
		{
			var resultChart:Object;
			
			switch(this.RenderEngine)
			{
				case ALChart.FLEX:
					resultChart = this.fbChart;
					break;
				case ALChart.AM:
					resultChart = this.abChart;
					break;
			}
				
			return resultChart;
		}
		
		public function GetFlexChart():BarChart
		{
			if (fbChart == null)
			{
				dbgText.text += "fbchart is null:";
			}
			else
			{
				//dbgText.text += "fbchart is NOT null:";
			}
				
			return fbChart;
		}
		
		public function GetAmChart():AmGraph
		{
			if (abChart == null)
			{
				dbgText.text += "fbchart is null:";
			}
			else
			{
				//dbgText.text += "fbchart is NOT null:";
			}
				
			return abChart;
		}
	}

}
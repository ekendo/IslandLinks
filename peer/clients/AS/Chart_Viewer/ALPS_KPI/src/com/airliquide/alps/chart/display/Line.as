package com.airliquide.alps.chart.display
{
	// FLEX SDK
	import mx.charts.*;
	import mx.charts.chartClasses.CartesianChart;
	import mx.charts.chartClasses.IAxis;
	import mx.charts.series.*;
	import mx.controls.Label;
	import mx.controls.Alert;
	import mx.collections.*;
	
	// AM SDK
	import com.amcharts.AmSerialChart;
	import com.amcharts.chartClasses.*;
	
	// Airliquide SDK
	import com.airliquide.alps.chart.*;
	
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class Line extends ChartDisplayFormat
	{
		private var verticalCategoryAxis:CategoryAxis;
		private var horizontalCategoryAxis:CategoryAxis;
		
		private var flexData:LineSeries;
		private var flChart:mx.charts.LineChart;
		private var alChart:AmGraph;
		
		private var series:Array;
		private var strokes:Array;
		private var colors:Array;
		
		private var useLineSeries:Boolean;
		
		public var dbgText:Label;
		
		public function Line() 
		{
			super();
			
			dbgText = new Label();
		}
		
		public function SetYAxisCategory(yca:CategoryAxis):void
		{
			this.verticalCategoryAxis = yca;
		}
		
		public function SetXAxisCategory(xca:CategoryAxis):void
		{
			this.horizontalCategoryAxis = xca;
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
		
		public function DrawLineChart():void
		{
			//dbgText.text += "In DrawBarChart";
			
			switch(this.RenderEngine)
			{
				case ALChart.FLEX:
					dbgText.text += "it is a FLEX chart:";
					//Alert.show("FLEX chart");
					flChart = new LineChart();
					
					// add bar series
					this.flChart.series = this.series;
					
					this.flChart.horizontalAxis = this.horizontalCategoryAxis;
					
					// set provider
					flChart.dataProvider = this.GetArrayCollectionData();
					
					break;
				case ALChart.AM:
					alChart = new AmGraph();
					
					this.alChart.type = "line";
					
					//this.acChart.valueField = horizontalValueAxis;
					
					this.alChart.dataProvider = this.GetArrayCollectionData();
					//Alert.show("returning abchart");
					break;
			}
		}
		
		public function GetFlexChart():LineChart
		{
			if (flChart == null)
			{
				dbgText.text += "fbchart is null:";
			}
			else
			{
				dbgText.text += "fbchart is NOT null:";
			}
				
			return flChart;
		}
	
	}
}
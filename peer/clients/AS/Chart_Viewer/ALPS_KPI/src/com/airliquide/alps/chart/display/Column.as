package com.airliquide.alps.chart.display
{
	// FLEX SDK
	import mx.charts.*;
	import mx.charts.chartClasses.CartesianChart;
	import mx.charts.chartClasses.IAxis;
	import mx.charts.series.*;
	import mx.controls.Label;
	
	// AM SDK
	import com.amcharts.AmSerialChart;
	import com.amcharts.chartClasses.*;
	
	// Airliquide SDK
	import com.airliquide.alps.chart.*;
	
	/**
	 * ...
	 * @author earl.lawrence
	 */
	public class Column extends ChartDisplayFormat
	{
		private var xAxisLen:int = 0;
		private var yAxisLen:int = 0;
		
		private var verticalCategoryAxis:CategoryAxis;
		private var horizontalCategoryAxis:CategoryAxis;
		
		private var flexData:ColumnSeries;
		private var fcChart:mx.charts.ColumnChart;
		private var acChart:AmGraph;
		
		private var series:Array;
		private var strokes:Array;
		private var colors:Array;
		
		private var useColumnSeries:Boolean;
		
		public var dbgText:Label;
		
		public function Column() 
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
		
		public function DrawColumnChart():void
		{
			//dbgText.text += "In DrawBarChart";
			
			switch(this.RenderEngine)
			{
				case ALChart.FLEX:
					dbgText.text += "it is a FLEX chart:";
					//Alert.show("FLEX chart");
					fcChart = new ColumnChart();
					
					this.fcChart.horizontalAxis = this.horizontalCategoryAxis;
					
					// add bar series
					this.fcChart.series = this.series;
					
					// set provider
					fcChart.dataProvider = this.GetArrayCollectionData();
					break;
				case ALChart.AM:
					acChart = new AmGraph();
					
					this.acChart.type = "column";
					
					//this.acChart.valueField = horizontalValueAxis;
					
					this.acChart.dataProvider = this.GetArrayCollectionData();
					//Alert.show("returning abchart");
					break;
			}
		}
		
		public function GetFlexChart():ColumnChart
		{
			if (fcChart == null)
			{
				//dbgText.text += "fcchart is null:";
			}
			else
			{
				//dbgText.text += "fbchart is NOT null:";
			}
				
			return fcChart;
		}
		
		public function GetAmChart():AmGraph
		{
			if (acChart == null)
			{
				//dbgText.text += "fcchart is null:";
			}
			else
			{
				//dbgText.text += "fbchart is NOT null:";
			}
				
			return acChart;
		}
	}

}
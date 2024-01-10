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
	public class ColumnArea extends ChartDisplayFormat
	{
		private var xAxisLen:int = 0;
		private var yAxisLen:int = 0;
		
		private var verticalCategoryAxis:CategoryAxis;
		private var horizontalCategoryAxis:CategoryAxis;
		
		private var flexData:ColumnSeries;
		private var fcChart:mx.charts.ColumnChart;
		private var acChart:AmGraph;
		private var faChart:mx.charts.AreaChart;
		private var acaChart:AmGraph;
		
		private var aSeries:Array;
		private var cSeries:Array;
		private var strokes:Array;
		private var colors:Array;
		
		private var useColumnSeries:Boolean;
		private var useAreaSeries:Boolean;
		
		public var dbgText:Label;
		
		public function ColumnArea() 
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
		
		public function SetAreaSeries(s:Array):void
		{
			this.aSeries = s;
		}
		
		public function SetColumnSeries(s:Array):void
		{
			this.cSeries = s;
		}
		
		public function DrawColumnAreaChart():void
		{
			//dbgText.text += "In DrawBarChart";
			
			switch(this.RenderEngine)
			{
				case ALChart.FLEX:
					
					fcChart = new ColumnChart();
					
					this.fcChart.horizontalAxis = this.horizontalCategoryAxis;
					
					// add bar series
					this.fcChart.series = this.cSeries;
					
					// set provider
					fcChart.dataProvider = this.GetArrayCollectionData();
					
					faChart = new AreaChart();
					
					this.faChart.horizontalAxis = this.horizontalCategoryAxis;
					
					// add bar series
					this.faChart.series = this.aSeries;
					
					// set provider
					faChart.dataProvider = this.GetArrayCollectionData();
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
		
		public function GetFlexColumnChart():ColumnChart
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
		
		public function GetFlexAreaChart():AreaChart
		{
			if (faChart == null)
			{
				//dbgText.text += "fcchart is null:";
			}
			else
			{
				//dbgText.text += "fbchart is NOT null:";
			}
				
			return faChart;
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
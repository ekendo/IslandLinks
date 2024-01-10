package org.islandlinks.Viewer.lang 
{
	
	import mx.controls.Alert;
	
	public class Map {
		private var size:int;
		private var keys:Array;
		private var values:Array;
		
		public function Map(){
			this.size = 0;
			this.keys = new Array();
			this.values = new Array();
		}
		
		public function length():int{
			return this.size;
		}
		
		public function getValueAt(index:int):String{
			if (index >= 0 && index < this.size){
				return this.values[index];
			}
			return null;
		}
		
		public function getEntryAt(index:int):MapEntry{
			if (index >= 0 && index < this.size){
				return new MapEntry(this.keys[index], this.values[index]);
			}
			return null;
		}
		
		public function getEntrySet():Array{
			var entrySet:Array = new Array(this.size);
			for (var i:int = 0; i < this.size; i++){
				entrySet[i] = this.getEntryAt(i);
			}
			return entrySet;
		}
		
		public function getValue(key:String):String{
			var i:int = this.keys.indexOf(key);
			if (i != -1){
				return this.values[i];	
			}
			return null;
		}
		
		public function setValue(key:String, value:String):void{
			var i:int = this.keys.indexOf(key);
			if (i != -1){
				this.values[i] = value;
				return;	
			}
			this.addPair(key, value);
		}
		
		public function getIndexOfKey(key:String):int{
			return this.keys.indexOf(key);
		}
		
		public function removeEntry(key:String):void{
			var i:int = this.keys.indexOf(key);
			if (i != -1){
				this.values.splice(i,1);
				this.keys.splice(i,1);
				this.size--;
			}
			
		}
		
		private function addPair(key:String, value:String):void{
			//Alert.show(key + " " + value);
			this.keys[this.size] = key;
			this.values[this.size] = value;
			this.size++;
		}
	}
}
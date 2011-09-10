/*
 *  Copyright (c) 2007 - 2011, CherryOnExt Team
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the CherryOnExt Team nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL CherryOnExt Team BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
Ext.namespace("Ext.ux.netbox.date");Ext.ux.netbox.date.DateTextEditor=function(b,a){Ext.ux.netbox.date.DateTextEditor.superclass.constructor.call(this,b,a);if(a.format==undefined){a.format="Y-m-d H:i:s"}this.format=a.format};Ext.extend(Ext.ux.netbox.date.DateTextEditor,Ext.ux.netbox.FilterEditor,{getValue:function(){var a=Ext.ux.netbox.date.DateTextEditor.superclass.getValue.call(this);if(a===""){return([])}else{return[{value:a.format("Y-m-d H:i:s"),label:a.format(this.format)}]}},setValue:function(b){var a;if(b.length==0){a=""}else{a=Date.parseDate(b[0].value,"Y-m-d H:i:s")}Ext.ux.netbox.date.DateTextEditor.superclass.setValue.call(this,a)}});Ext.namespace("Ext.ux.netbox.date");Ext.ux.netbox.date.DateOperator=function(c,a,b){Ext.ux.netbox.date.DateOperator.superclass.constructor.call(this,c,a,b);this.editor=null;this.format=b};Ext.extend(Ext.ux.netbox.date.DateOperator,Ext.ux.netbox.core.Operator,{createEditor:function(b){var d;var a=this.format.split(" ");if(a.length>1){var c=new Ext.ux.form.DateTime({dateFormat:a[0],dateConfig:{altFormats:"Y-m-d|Y-n-d"},otherToNow:false,timeFormat:a[1],timeConfig:{altFormats:"H:i:s"}});d=new Ext.ux.netbox.date.DateTextEditor(c,{format:this.format})}else{d=new Ext.ux.netbox.date.DateTextEditor(new Ext.form.DateField({format:a[0],allowBlank:false}),{format:this.format})}return d},convertValue:function(a){if(a!==null&&a!==undefined&&Ext.type(a)=="array"){if(a.length>0&&a[0].value!==undefined&&a[0].label!==undefined){if(this.getField().checkDate(a[0].label)&&this.getField().checkDate(a[0].value,"Y-m-d H:i:s")){if(a.length==1){return(a)}else{return([a[0]])}}}}return([])},getFormat:function(){return this.format}});Ext.ux.netbox.date.DateRangeEditor=function(b,a){Ext.ux.netbox.date.DateRangeEditor.superclass.constructor.call(this,b,a);this.format=a.format};Ext.extend(Ext.ux.netbox.date.DateRangeEditor,Ext.ux.netbox.FilterEditor,{getValue:function(){var d=Ext.ux.netbox.date.DateRangeEditor.superclass.getValue.call(this);var c=[];for(var b=0;d&&b<d.length;b++){var a=Date.parseDate(d[b].value,this.format);if(!a){c.push({label:"",value:""});continue}d[b].value=a.format("Y-m-d H:i:s");c.push(d[b])}return(c)}});Ext.ux.netbox.date.DateRangeOperator=function(b){Ext.ux.netbox.date.DateRangeOperator.superclass.constructor.call(this,"DATE_RANGE",this.includeText,b);this.mapping={d:"99",m:"99",Y:"9999",y:"99",H:"99",i:"99",s:"99"};var a=function(f){var d=this.getField().emptyNotAllowedFn(f);if(d!==true){return(d)}if(f.length!=2){return(this.bothFromAndToNotEmpty)}var c=this.getField().checkDate(f[0].value,"Y-m-d H:i:s");var e=this.getField().checkDate(f[1].value,"Y-m-d H:i:s");if(!c&&!e){return(this.toAndFromNotADate)}if(!c){return(this.fromNotADate)}if(!e){return(this.toNotADate)}if(Date.parseDate(f[0].value,"Y-m-d H:i:s")>Date.parseDate(f[1].value,"Y-m-d H:i:s")){return(this.fromBiggerThanTo)}return(true)};this.setValidateFn(a)};Ext.extend(Ext.ux.netbox.date.DateRangeOperator,Ext.ux.netbox.date.DateOperator,{fromText:"from",toText:"to",includeText:"between",bothFromAndToNotEmpty:"Both 'from' and 'to' must have a value",fromBiggerThanTo:"From is bigger than to",fromNotADate:"From is not a valid date",toNotADate:"To is not a valid date",toAndFromNotADate:"From and to are not valid dates",createEditor:function(a){var c=new Ext.ux.netbox.core.RangeField({textCls:Ext.form.TextField,fromConfig:this.getTextFieldConfig(),toConfig:this.getTextFieldConfig(),minListWidth:300,fieldSize:36});var b=new Ext.ux.netbox.date.DateRangeEditor(c,{format:this.format});c.on("editingcompleted",b.completeEdit,b);return b},render:function(c){var b=c[0]==undefined?"":c[0].label;var a=c[1]==undefined?"":c[1].label;return(this.fromText+": "+b+", "+this.toText+": "+a)},getTextFieldConfig:function(){return({plugins:[new Ext.ux.netbox.InputTextMask(this.calculateMask(),true)]})},calculateMask:function(){var b="";for(var a=0;a<this.format.length;a++){if(this.mapping[this.format.charAt(a)]){b+=this.mapping[this.format.charAt(a)]}else{b+=this.format.charAt(a)}}return(b)}});Ext.ux.netbox.date.DatePeriodOperator=function(){Ext.ux.netbox.date.DatePeriodOperator.superclass.constructor.call(this,"DATE_PERIOD",this.periodText);this.periodStore=new Ext.data.SimpleStore({fields:["value","label"],data:[["LAST_QUARTER",this.quarterText],["LAST_HOUR",this.hourText],["LAST_DAY",this.dayText],["LAST_WEEK",this.weekText],["LAST_MONTH",this.monthText],["LAST_YEAR",this.yearText]]});var a=function(b){if(this.getField().emptyNotAllowedFn(b)!==true){return(this.getField().emptyNotAllowedFn(b))}if(b[0].value!=="LAST_QUARTER"&&b[0].value!=="LAST_HOUR"&&b[0].value!=="LAST_DAY"&&b[0].value!=="LAST_WEEK"&&b[0].value!=="LAST_MONTH"&&b[0].value!=="LAST_YEAR"){return(this.valueNotExpected)}return(true)};this.setValidateFn(a)};Ext.extend(Ext.ux.netbox.date.DatePeriodOperator,Ext.ux.netbox.core.Operator,{periodText:"period",yearText:"last year",monthText:"last month",weekText:"last week",dayText:"last day",hourText:"last hour",quarterText:"last quarter",valueNotExpected:"Value not expected",getDefaultValues:function(){return([{value:"LAST_DAY",label:this.dayText}])},setPeriods:function(a){this.periodStore=a;this.editor=null},createEditor:function(a){var b=new Ext.ux.netbox.core.AvailableValuesEditor(this.periodStore);return b},convertValue:function(a){if(a!==null&&a!==undefined&&Ext.type(a)=="array"){if(a.length>0&&a[0].value!==undefined&&a[0].label!==undefined){if(this.periodStore.find("value",a[0].value)!="-1"){if(a.length==1){return(a)}else{return([a[0]])}}}}return([])}});Ext.namespace("Ext.ux.netbox.date");Ext.ux.netbox.date.DateField=function(e,a,b){Ext.ux.netbox.date.DateField.superclass.constructor.call(this,e,a);this.setValidateFn(this.validateDate);var c=new Ext.ux.netbox.date.DatePeriodOperator();this.addOperator(c);this.setDefaultOperator(c);this.addOperator(new Ext.ux.netbox.date.DateOperator("DATE_EQUAL","=",b));noEmptyAllowed=this.emptyNotAllowedFn.createDelegate(this);var d=new Ext.ux.netbox.date.DateOperator("DATE_GREATER",">",b);d.addValidateFn(noEmptyAllowed);this.addOperator(d);d=new Ext.ux.netbox.date.DateOperator("DATE_GREATER_OR_EQUAL",">=",b);d.addValidateFn(noEmptyAllowed);this.addOperator(d);d=new Ext.ux.netbox.date.DateOperator("DATE_LESS","<",b);d.addValidateFn(noEmptyAllowed);this.addOperator(d);d=new Ext.ux.netbox.date.DateOperator("DATE_LESS_OR_EQUAL","<=",b);d.addValidateFn(noEmptyAllowed);this.addOperator(d);this.addOperator(new Ext.ux.netbox.date.DateRangeOperator(b));this.format=b};Ext.extend(Ext.ux.netbox.date.DateField,Ext.ux.netbox.core.Field,{validateDate:function(a){for(var b=0;a&&b<a.length;b++){if(a[b].value!==""&&!this.checkDate(a[b].value,"Y-m-d H:i:s")){return(this.checkDate(a[b].value,"Y-m-d H:i:s"))}}return(true)},checkDate:function(c,d){if(d==undefined){d=this.format}var a=Date.parseDate(c,d);if(!a){return(false)}var b=a.format(d);if(c!=b){return(false)}return(true)}});
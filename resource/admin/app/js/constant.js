angular.module('xenon.constant', []).constant('ASSETS', {
	'core': {
		'bootstrap': tinyConfig.assetPath('js/bootstrap.min.js'), // Some plugins which do not support angular needs this

		'jQueryUI': [
			tinyConfig.assetPath('js/jquery-ui/jquery-ui.min.js'),
			tinyConfig.assetPath('js/jquery-ui/jquery-ui.structure.min.css'),
		],

		'moment': tinyConfig.assetPath('js/moment.min.js'),

		'googleMapsLoader': tinyConfig.assetPath('app/js/angular-google-maps/load-google-maps.js')
	},
    
    'plupload': tinyConfig.assetPath('js/plupload.full.min.js'),

	'charts': {

		'dxGlobalize': tinyConfig.assetPath('js/devexpress-web-14.1/js/globalize.min.js'),
		'dxCharts': tinyConfig.assetPath('js/devexpress-web-14.1/js/dx.chartjs.js'),
		'dxVMWorld': tinyConfig.assetPath('js/devexpress-web-14.1/js/vectormap-data/world.js'),
	},

	'xenonLib': {
		notes: tinyConfig.assetPath('js/xenon-notes.js'),
	},

	'maps': {

		'vectorMaps': [
			tinyConfig.assetPath('js/jvectormap/jquery-jvectormap-1.2.2.min.js'),
			tinyConfig.assetPath('js/jvectormap/regions/jquery-jvectormap-world-mill-en.js'),
			tinyConfig.assetPath('js/jvectormap/regions/jquery-jvectormap-it-mill-en.js'),
		],
	},

	'icons': {
		'meteocons': tinyConfig.assetPath('css/fonts/meteocons/css/meteocons.css'),
		'elusive': tinyConfig.assetPath('css/fonts/elusive/css/elusive.css'),
	},

	'tables': {
		'rwd': tinyConfig.assetPath('js/rwd-table/js/rwd-table.min.js'),

		'datatables': [
			tinyConfig.assetPath('js/datatables/dataTables.bootstrap.css'),
			tinyConfig.assetPath('js/datatables/datatables-angular.js'),
		],

	},

	'forms': {

		'select2': [
			tinyConfig.assetPath('js/select2/select2.css'),
			tinyConfig.assetPath('js/select2/select2-bootstrap.css'),

			tinyConfig.assetPath('js/select2/select2.min.js'),
		],

		'daterangepicker': [
			tinyConfig.assetPath('js/daterangepicker/daterangepicker-bs3.css'),
			tinyConfig.assetPath('js/daterangepicker/daterangepicker.js'),
		],

		'colorpicker': tinyConfig.assetPath('js/colorpicker/bootstrap-colorpicker.min.js'),

		'selectboxit': tinyConfig.assetPath('js/selectboxit/jquery.selectBoxIt.js'),

		'tagsinput': tinyConfig.assetPath('js/tagsinput/bootstrap-tagsinput.min.js'),

		'datepicker': tinyConfig.assetPath('js/datepicker/bootstrap-datepicker.js'),

		'timepicker': tinyConfig.assetPath('js/timepicker/bootstrap-timepicker.min.js'),

		'inputmask': tinyConfig.assetPath('js/inputmask/jquery.inputmask.bundle.js'),

		'formWizard': tinyConfig.assetPath('js/formwizard/jquery.bootstrap.wizard.min.js'),

		'jQueryValidate': tinyConfig.assetPath('js/jquery-validate/jquery.validate.min.js'),

		'dropzone': [
			tinyConfig.assetPath('js/dropzone/css/dropzone.css'),
			tinyConfig.assetPath('js/dropzone/dropzone.min.js'),
		],

		'typeahead': [
			tinyConfig.assetPath('js/typeahead.bundle.js'),
			tinyConfig.assetPath('js/handlebars.min.js'),
		],

		'multiSelect': [
			tinyConfig.assetPath('js/multiselect/css/multi-select.css'),
			tinyConfig.assetPath('js/multiselect/js/jquery.multi-select.js'),
		],

		'icheck': [
			tinyConfig.assetPath('js/icheck/skins/all.css'),
			tinyConfig.assetPath('js/icheck/icheck.min.js'),
		],

		'bootstrapWysihtml5': [
			tinyConfig.assetPath('js/wysihtml5/src/bootstrap-wysihtml5.css'),
			tinyConfig.assetPath('js/wysihtml5/wysihtml5-angular.js')
		],
	},

	'uikit': {
		'base': [
			tinyConfig.assetPath('js/uikit/uikit.css'),
			tinyConfig.assetPath('js/uikit/css/addons/uikit.almost-flat.addons.min.css'),
			tinyConfig.assetPath('js/uikit/js/uikit.min.js'),
		],

		'codemirror': [
			tinyConfig.assetPath('js/uikit/vendor/codemirror/codemirror.js'),
			tinyConfig.assetPath('js/uikit/vendor/codemirror/codemirror.css'),
		],

		'marked': tinyConfig.assetPath('js/uikit/vendor/marked.js'),
		'htmleditor': tinyConfig.assetPath('js/uikit/js/addons/htmleditor.min.js'),
		'nestable': tinyConfig.assetPath('js/uikit/js/addons/nestable.min.js'),
	},

	'extra': {
		'tocify': tinyConfig.assetPath('js/tocify/jquery.tocify.min.js'),

		'toastr': tinyConfig.assetPath('js/toastr/toastr.min.js'),

		'fullCalendar': [
			tinyConfig.assetPath('js/fullcalendar/fullcalendar.min.css'),
			tinyConfig.assetPath('js/fullcalendar/fullcalendar.min.js'),
		],

		'cropper': [
			tinyConfig.assetPath('js/cropper/cropper.min.js'),
			tinyConfig.assetPath('js/cropper/cropper.min.css'),
		]
	}
});
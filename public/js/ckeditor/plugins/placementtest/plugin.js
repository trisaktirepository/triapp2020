(function() {
	var o = { exec: function(p) {
			CKEDITOR.instances[CKEDITOR.currentInstance.name].insertHtml('[Candidate]');
			
		}
	};
	
	var o2 = { exec: function(p) {
			CKEDITOR.instances[CKEDITOR.currentInstance.name].insertHtml('[EmailApplicant]');
			
		}
	};
	
	var o3 = { exec: function(p) {
			CKEDITOR.instances[CKEDITOR.currentInstance.name].insertHtml('[PassApplicant]');
			
		}
	};
	
	var o4 = { exec: function(p) {
		CKEDITOR.instances[CKEDITOR.currentInstance.name].insertHtml('[FIRST_NAME]');
		
		}
	};
	
	var o5 = { exec: function(p) {
		CKEDITOR.instances[CKEDITOR.currentInstance.name].insertHtml('[MIDDLE_NAME]');
		
		}
	};
	
	var o6 = { exec: function(p) {
		CKEDITOR.instances[CKEDITOR.currentInstance.name].insertHtml('[LAST_NAME]');
		
		}
	};
		
	CKEDITOR.plugins.add('placementtest', {
		init: function(editor) {
				
				editor.addCommand('fn_app_name', o);
				editor.addCommand('fn_app_email', o2);
				editor.addCommand('fn_app_password', o3);
				editor.addCommand('fn_app_fname', o4);
				editor.addCommand('fn_app_mname', o5);
				editor.addCommand('fn_app_lname', o6);
				
				editor.ui.addButton('btn1', {
					label: 'Applicant Name',
					'title' : 'Candidate name',
					icon: this.path + 'newplugin.png',
					command: 'fn_app_name'
				});
				
				editor.ui.addButton('btn2', {
					label: "Applicant's Email",
					icon: this.path + 'newplugin.png',
					command: 'fn_app_email'
				});
				
				editor.ui.addButton('btn3', {
					label: "Applicant's Password",
					icon: this.path + 'newplugin.png',
					command: 'fn_app_password'
				});
				
				editor.ui.addButton('btn4', {
					label: 'Applicant First Name',
					'title' : 'Candidate First name',
					icon: this.path + 'newplugin.png',
					command: 'fn_app_fname'
				});
				
				editor.ui.addButton('btn5', {
					label: 'Applicant Middle Name',
					'title' : 'Candidate Middle name',
					icon: this.path + 'newplugin.png',
					command: 'fn_app_mname'
				});
				
				editor.ui.addButton('btn6', {
					label: 'Applicant Middle Name',
					'title' : 'Candidate Last name',
					icon: this.path + 'newplugin.png',
					command: 'fn_app_lname'
				});
		}
	});

})();
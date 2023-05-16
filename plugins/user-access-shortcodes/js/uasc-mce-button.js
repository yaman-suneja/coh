(function() {
	tinymce.PluginManager.add('uasc_mce_button', function( editor, url ) {
		editor.addButton( 'uasc_mce_button', {
			icon: 'uasc-mce-icon',
            tooltip: 'User Access Shortcodes',
			type: 'menubutton',
			menu: [
				{
				    text: 'Guests content',
				    onclick: function() {
                        editor.windowManager.open( {
							title: 'Show content to guests only',
							body: [
								{
									type: 'textbox',
									name: 'included',
									label: 'Include users by ID (comma separated)',
								},
								{
									type: 'listbox',
									name: 'guests_admin_can',
									label: 'Should the admin be able to see this content?',
									'values': [
										{text: 'Yes', value: '1'},
										{text: 'No', value: '0'}
									]
								}
							],
							onsubmit: function( e ) {
                editor.insertContent('[UAS_guest in="' + e.data.included + '" admin="' + e.data.guests_admin_can + '"]<br/>This content can only be seen by guests.<br/>[/UAS_guest]');
							}
				        });	
				      },
                },
                {
				    text: 'Logged in users content',
				    onclick: function() {
				        editor.windowManager.open( {
							title: 'Show content to logged in users only',
							body: [
								{
									type: 'textbox',
									name: 'excluded',
									label: 'Exclude users by ID (comma separated)',
								},
							],
							onsubmit: function( e ) {
                editor.insertContent('[UAS_loggedin ex="' + e.data.excluded + '"]<br/>This content can only be seen by logged in users.<br/>[/UAS_loggedin]');
							}
				        });	
				    },
                },

                {
                  text: 'Specific roles content',
                  onclick: function() {
                              editor.windowManager.open( {
                    title: 'Show content only to specific roles',
                    body: [
                      {
                        type: 'textbox',
                        name: 'allowedRoles',
                        label: 'Allowed roles (comma separated)',
                      },
                    ],
                    onsubmit: function( e ) {
                      editor.insertContent('[UAS_role roles="' + e.data.allowedRoles + '"]<br/>This content can only be seen by users with specific roles.<br/>[/UAS_role]');
                    }
                      });	
                    },
                      },

                {
				    text: 'Users by ID content',
				    onclick: function() {
				        editor.windowManager.open( {
							title: 'Show content only to specific users',
							body: [
								{
									type: 'textbox',
									name: 'specific',
									label: 'Select users by ID (comma separated)',
								},
                                {
									type: 'listbox',
									name: 'specific_admin_can',
									label: 'Should the admin be able to see this content?',
									'values': [
										{text: 'Yes', value: '1'},
										{text: 'No', value: '0'}
									]
								}
							],
							onsubmit: function( e ) {
                editor.insertContent('[UAS_specific ids="' + e.data.specific + '" admin="' + e.data.specific_admin_can + '"]<br/>This content can only be seen by some selected users.<br/>[/UAS_specific]');
							}
				        });	
				    },
                },
			]
		});
	});
})();
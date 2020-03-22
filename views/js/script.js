var GLOBALS = [];

$(document).ready(()=>{
    $('.ui.form')
        .form({
                fields: {
                    title: {
                        identifier: 'title',
                        rules: [
                            {
                                type: 'empty',
                                prompt: 'Please enter a title'
                            },
                            {
                                type: 'maxLength[40]',
                                prompt: 'Title can\'t be longer than 40 characters'
                            }
                        ]
                    },
                    tags: {
                        identifier: 'tags',
                        rules: [
                            {
                                type: 'empty',
                                prompt: 'Please enter a tags'
                            },
                            {
                                type: 'maxLength[1000]',
                                prompt: 'Tags can\'t be longer than 1000 characters'
                            }
                        ]
                    },
                    username: {
                        identifier: 'username',
                        rules: [
                            {
                                type: 'maxLength[20]',
                                prompt: 'Username can\'t be longer than 20 characters'
                            }
                        ]
                    },
                    name: {
                        identifier: 'name',
                        rules: [
                            {
                                type: 'maxLength[40]',
                                prompt: 'Name can\'t be longer than 40 characters'
                            }
                        ]
                    }
                }
            }
        );
    $('table').tablesort();
});





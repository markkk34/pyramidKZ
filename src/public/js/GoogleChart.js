google.charts.load(
    'current',
    {
        packages :
            ["orgchart"]
    });

google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Name');
    data.addColumn('string', 'Manager');
    data.addColumn('datetime', 'ToolTip');

    data.addRow([
        {
            'v':
                persons[0].entity_id.toString(),
            'f':
                '<div class="fas fa-crown president">' +
                '<br>' +
                persons[0].position.toString() +
                '<br>' +
                persons[0].firstname.toString() +
                ' ' +
                persons[0].lastname.toString() +
                '<br>' +
                persons[0].email.toString() +
                '<br>' +
                persons[0].shares_amount.toString() +
                ' stocks' +
                '<br>' +
                new Date(persons[0].start_date).toString().replace(/(.+?\GMT).*/, "$1").replace('GMT', '') +
                '</div>'
        },
        '',
        new Date(persons[0].start_date)
    ]);

    for (let i = 1; i < persons.length; i++) {
        let position = 'fas fa-user novice';
        switch (persons[i].position.toString()) {
            case 'vice president':
                position = 'fab fa-vine vice-president';
                break;
            case 'manager':
                position = 'fas fa-snowman manager';
                break;
        }
        data.addRow([
            {
                'v':
                    persons[i].entity_id.toString(),
                'f':
                    '<div class=" ' +
                    position +
                    '">' +
                    '<br>' +
                    persons[i].position.toString() +
                    '<br>' +
                    persons[i].firstname.toString() +
                    ' ' +
                    persons[i].lastname.toString() +
                    '<br>' +
                    persons[i].email.toString() +
                    '<br>' +
                    persons[i].shares_amount.toString() +
                    ' stocks' +
                    '<br>' +
                    new Date(persons[i].start_date).toString().replace(/(.+?\GMT).*/, "$1").replace('GMT', '') +
                    '</div>'
            },
            persons[i].parent_id.toString(),
            new Date(persons[i].start_date)
        ]);
    }
    
    // Create the chart.
    var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
    // Draw the chart, setting the allowHtml option to true for the tooltips.
    chart.draw(data, {'allowHtml':true, 'allowCollapse':true, 'size':'large'});

/*    google.visualization.events.addListener(chart, 'select', selectHandler);

// Notice that e is not used or needed.
    function selectHandler(e) {
        alert('The user selected' + chart.getSelection().length + ' items.');
    }*/
}


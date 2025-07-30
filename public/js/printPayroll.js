
function printPayroll() {
    var content = document.getElementById('payrollContent').innerHTML;

    var printWindow = window.open('', '', 'height=600,width=800');

    printWindow.document.write('<html><head><title>Payroll Report</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">'); // Optional: Tailwind CSS for styling
    printWindow.document.write('<style>body { font-family: Arial, sans-serif; } .payroll-table { width: 100%; border-collapse: collapse; } .payroll-table th, .payroll-table td { padding: 10px; border: 1px solid #ddd; }</style>'); // Optional custom styles
    printWindow.document.write('</head><body>');
    printWindow.document.write(content); 
    printWindow.document.write('</body></html>');
    printWindow.document.close(); 

    printWindow.print();
}

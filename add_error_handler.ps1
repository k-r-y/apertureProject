$file = "src\admin\inquiries.php"
$content = Get-Content $file -Raw

# Add .catch() to loadActivityLog function
$content = $content -replace '(\s+}\s+\)\;\s+}\s+function formatActivityType)', `
'$1
                })
                .catch(error => {
                    console.error(''Activity log error:'', error);
                    document.getElementById(''activityTableBody'').innerHTML = 
                        ''<tr><td colspan="5" class="text-center text-danger">Error loading activities</td></tr>'';
                });
        }

        function formatActivityType'

$content | Set-Content $file -NoNewline
Write-Host "Added error handler to loadActivityLog!" -ForegroundColor Green

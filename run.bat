

:: This is the array to loop through for startpage

for %%s in (
"football"
"school"
"computer science"
"software development"
) do php run.php -k '%%s' -a startpage -d 3 -r 10 -v



:: This is the array to loop through for correlate

for %%s in (
"soccer"
"backetball"
"barbeque"
"vaction"
) do php run.php -k '%%s' -a correlate -d 3 -r 10 -v

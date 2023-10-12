#! /bin/awk -f
# Run with `cat name.tsv | gawk -f tsv-to-csv.awk > outfile.csv`
#   or `mysql -b -e "select * from user" db_name | gawk -f tsv-to-csv.gawk > outfile.csv`

BEGIN { inView=0 }


/.*CREATE VIEW/ {
  inView=1
}

{
  if (!inView) {
    print $0
  }
}

/\*\/;$/ {
  inView=0
}


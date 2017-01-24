serbia_list = []

file = open("data/country_data.csv", "r")
for line in file:
    line_list = line.split(',')
    if "SERBIA" in line_list[1]:
        serbia_list.append((line_list[0]).strip('"'))
print(serbia_list)

filter_file = open("data/filter.csv", 'w')
for line in serbia_list:
    filter_file.write("{}\n".format(line))
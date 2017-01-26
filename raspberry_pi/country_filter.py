import os

option = input("Do you want to empty the country filter? (yes/no)\n")
if option == "yes":
    os.remove("data/filter.csv")

option = input("Do you want to add a country to the filter? (yes/no)\n")
if option == "yes":
    country = input("What country do you want to add? (cancel to cancel)\n")
    if country == "cancel":
        print("Goodbye!")
        exit(0)
    else:
        serbia_list = []

        file = open("data/country_data.csv", "r")
        for line in file:
            line_list = line.split(',')
            if country.upper() in line_list[1]:
                serbia_list.append((line_list[0]).strip('"'))

        print("These stations will be added:")

        filter_file = open("data/filter.csv", 'w')
        for line in serbia_list:
            print(line)
            filter_file.write("{}\n".format(line))

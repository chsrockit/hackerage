import simplejson

matches = []

reviews_data= open('fest.json')
reviews = simplejson.load(reviews_data)
reviews_data.close()

shows_data= open('listings.json')
shows = simplejson.load(shows_data)
shows_data.close()

print "We have %d reviews for %d shows." % (len(reviews), len(shows))

show_names = []
for show in shows:
    show_names.append((show['id'], show['event_desc']))

for review in reviews:
    name = review['review']['name']
    for show in show_names:
        if show[1] == name:
            matches.append((show[0],name))
            #print "("+show[1]+", "+name+")"
            continue

print "%d reviews were matched" % (len(matches))



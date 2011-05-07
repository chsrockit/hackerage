import simplejson
import string

from BeautifulSoup import BeautifulStoneSoup

matches = []

reviews_data= open('fest.json')
reviews = simplejson.load(reviews_data)
reviews_data.close()

shows_data= open('listings.json')
shows = simplejson.load(shows_data)
shows_data.close()

show_names = []
failures = []

for show in shows:
    if show['area_code'] == "EFF":
        show_names.append((show['id'], show['event_desc']))

print "We have %d reviews for %d shows." % (len(reviews), len(show_names))

for review in reviews:
    success = 0

    name = review['review']['name']
    name = unicode(BeautifulStoneSoup(name,convertEntities=BeautifulStoneSoup.HTML_ENTITIES ))
    for c in string.punctuation:
        name = name.replace(c,"")
    name = string.lower(name) 

    for show in show_names:
        show_name = show[1]
        for c in string.punctuation:
            show_name = show_name.replace(c,"")
        show_name = string.lower(show_name) 
        show_name = unicode(BeautifulStoneSoup(show_name,convertEntities=BeautifulStoneSoup.HTML_ENTITIES ))

        if not [word for word in name.split(' ') if word not in show_name.split(' ')]:
            matches.append((show[0], review['review']['name'])
            success = 1
            #if show[1] != review['review']['name']:
            #    print "("+show[1]+", "+review['review']['name']+")"
            break
        else:
            failures.append(review['review']['name'])

    #if not success:
    #    print review['review']['name']

print "%d reviews were matched" % (len(matches))
print "%d reviews were not matched" % (len(failures))

print failures


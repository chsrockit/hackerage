import simplejson
import string
import difflib

#from BeautifulSoup import BeautifulStoneSoup

matches = []

#reviews_data= open('fest.json')
#reviews_data= open('listreviews.json')
reviews_data= open('threeweeksreviews.json')
reviews = simplejson.load(reviews_data)
reviews_data.close()

shows_data= open('listings.json')
shows = simplejson.load(shows_data)
shows_data.close()

show_names = []
#failures = []

for show in shows:
    if show['area_code'] == "EFF":
        show_names.append((show['id'], show['event_desc']))

print "We have %d reviews for %d shows." % (len(reviews), len(show_names))

for review in reviews:
    success = 0

    name = review['review']['name']
    #name = unicode(BeautifulStoneSoup(name,convertEntities=BeautifulStoneSoup.HTML_ENTITIES ))
    for c in string.punctuation:
        name = name.replace(c,"")
    name = string.lower(name) 

    hiscore = 0
    bestmatch = ""
    for show in show_names:

        show_name = show[1]
        for c in string.punctuation:
            show_name = show_name.replace(c,"")
        show_name = string.lower(show_name) 
        #show_name = unicode(BeautifulStoneSoup(show_name,convertEntities=BeautifulStoneSoup.HTML_ENTITIES ))

        ratio = difflib.SequenceMatcher(None, name, show_name).ratio()
        if not [word for word in name.split(' ') if word not in show_name.split(' ')] or ratio > 0.80 or name in show_name:
            matches.append((show[0], review['review']['name']))
            success = 1
            #print ratio
            #if show[1] != review['review']['name']:
            #    print "("+show[1]+", "+review['review']['name']+")"
            break
        else:
            if ratio >= hiscore:
                hiscore = ratio
                bestmatch = show_name

    if not success:
        #print name.split(' ')
        #failures.append(review['review']['name'])
        if hiscore > 0.6:
            print name
            print bestmatch+" "+str(hiscore)

f = open('threeweeks_matches.txt', 'w')
f.write(str(matches))
f.close()

print "%d reviews were matched" % (len(matches))
#print "%d reviews were not matched" % (len(failures))

#print failures


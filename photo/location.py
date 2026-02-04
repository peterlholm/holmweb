"reverse location info"
from geopy.geocoders import Nominatim

# nominatim
g = Nominatim(user_agent="dias-information", timeout=10)

def reverse_location (lat, long):
    "get address for location"
    address = (lat, long)
    l = g.reverse(address)
    print (l)
    return l.address


if __name__=='__main__':
    address = (55.725158, 12.47673)
    add = reverse_location(address[0],address[1])
    print(add)
    print(add.address)


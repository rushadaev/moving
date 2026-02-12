import { ArrowRight } from 'lucide-react';
import { Button } from './components/ui/button';

export default function App() {
  return (
    <div className="min-h-screen bg-white">
      {/* Navigation - Apple style */}
      <nav className="fixed top-0 w-full bg-white/80 backdrop-blur-xl z-50 border-b border-black/10">
        <div className="max-w-[980px] mx-auto px-6">
          <div className="flex items-center justify-between h-11">
            <div className="text-xl font-semibold tracking-tight">moowwee</div>
            <div className="hidden md:flex items-center gap-8 text-xs font-medium">
              <a href="#residential" className="hover:text-gray-600 transition">Residential</a>
              <a href="#commercial" className="hover:text-gray-600 transition">Commercial</a>
              <a href="#storage" className="hover:text-gray-600 transition">Storage</a>
              <a href="#support" className="hover:text-gray-600 transition">Support</a>
            </div>
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <section className="pt-24 pb-12">
        <div className="max-w-[980px] mx-auto px-6 text-center">
          <h1 className="text-5xl md:text-6xl lg:text-7xl font-semibold tracking-tight mb-4">
            moowwee
          </h1>
          <p className="text-2xl md:text-3xl text-gray-600 mb-6">
            Moving reimagined.
          </p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center items-center text-lg mb-3">
            <span>From $299</span>
            <Button variant="link" className="text-blue-600 p-0 h-auto">
              Get a quote <ArrowRight className="ml-1 size-4" />
            </Button>
          </div>
        </div>
      </section>

      {/* Large Hero Image */}
      <section className="pb-20">
        <div className="max-w-[1200px] mx-auto px-6">
          <div className="w-full aspect-[16/9] rounded-2xl overflow-hidden">
            <img 
              src="https://images.unsplash.com/photo-1574227136494-46cf4c368a4e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtaW5pbWFsaXN0JTIwd2hpdGUlMjByb29tJTIwYnJpZ2h0fGVufDF8fHx8MTc3MDg3MTk4Mnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
              alt="Modern interior"
              className="w-full h-full object-cover"
            />
          </div>
        </div>
      </section>

      {/* Feature Callout 1 */}
      <section className="py-20 bg-black text-white">
        <div className="max-w-[980px] mx-auto px-6 text-center">
          <h2 className="text-4xl md:text-6xl font-semibold mb-4 tracking-tight">
            Seamless. From start to finish.
          </h2>
          <p className="text-xl md:text-2xl text-gray-400">
            Experience moving the way it should be.
          </p>
        </div>
      </section>

      {/* Feature Section - Residential */}
      <section id="residential" className="py-20">
        <div className="max-w-[980px] mx-auto px-6">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <div className="text-sm font-semibold text-orange-600 mb-2">RESIDENTIAL</div>
              <h2 className="text-4xl md:text-5xl font-semibold mb-6 tracking-tight">
                Your home.<br />Our priority.
              </h2>
              <p className="text-xl text-gray-600 leading-relaxed mb-8">
                From packing your first box to placing the last piece of furniture, 
                our team handles every detail with care. Because your belongings aren't just things—they're your life.
              </p>
              <div className="space-y-3 text-lg">
                <div className="flex items-center gap-3">
                  <div className="w-1 h-1 bg-black rounded-full"></div>
                  <span>Full-service packing</span>
                </div>
                <div className="flex items-center gap-3">
                  <div className="w-1 h-1 bg-black rounded-full"></div>
                  <span>Climate-controlled transport</span>
                </div>
                <div className="flex items-center gap-3">
                  <div className="w-1 h-1 bg-black rounded-full"></div>
                  <span>White-glove service</span>
                </div>
              </div>
            </div>
            <div className="aspect-square rounded-2xl overflow-hidden">
              <img 
                src="https://images.unsplash.com/photo-1606932250069-62f395a08602?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxoYXBweSUyMGZhbWlseSUyMG5ldyUyMGhvbWV8ZW58MXx8fHwxNzcwODcxOTgzfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                alt="Happy family"
                className="w-full h-full object-cover"
              />
            </div>
          </div>
        </div>
      </section>

      {/* Feature Section - Commercial */}
      <section id="commercial" className="py-20 bg-gray-50">
        <div className="max-w-[980px] mx-auto px-6">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div className="order-2 md:order-1 aspect-square rounded-2xl overflow-hidden">
              <img 
                src="https://images.unsplash.com/photo-1718066236079-9085195c389e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBhcGFydG1lbnQlMjBlbXB0eSUyMGJyaWdodHxlbnwxfHx8fDE3NzA4NzE5ODN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                alt="Modern office"
                className="w-full h-full object-cover"
              />
            </div>
            <div className="order-1 md:order-2">
              <div className="text-sm font-semibold text-blue-600 mb-2">COMMERCIAL</div>
              <h2 className="text-4xl md:text-5xl font-semibold mb-6 tracking-tight">
                Business moves.<br />Zero downtime.
              </h2>
              <p className="text-xl text-gray-600 leading-relaxed mb-8">
                We coordinate every aspect of your office relocation. Your team can focus on what matters 
                while we handle the logistics.
              </p>
              <div className="space-y-3 text-lg">
                <div className="flex items-center gap-3">
                  <div className="w-1 h-1 bg-black rounded-full"></div>
                  <span>After-hours moving</span>
                </div>
                <div className="flex items-center gap-3">
                  <div className="w-1 h-1 bg-black rounded-full"></div>
                  <span>IT equipment specialists</span>
                </div>
                <div className="flex items-center gap-3">
                  <div className="w-1 h-1 bg-black rounded-full"></div>
                  <span>Project management</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-20">
        <div className="max-w-[980px] mx-auto px-6">
          <div className="grid grid-cols-3 gap-8 text-center">
            <div>
              <div className="text-5xl md:text-6xl font-semibold mb-2">10K+</div>
              <div className="text-gray-600">Moves completed</div>
            </div>
            <div>
              <div className="text-5xl md:text-6xl font-semibold mb-2">4.9</div>
              <div className="text-gray-600">Customer rating</div>
            </div>
            <div>
              <div className="text-5xl md:text-6xl font-semibold mb-2">100%</div>
              <div className="text-gray-600">Insured</div>
            </div>
          </div>
        </div>
      </section>

      {/* Storage Section */}
      <section id="storage" className="py-20 bg-gray-50">
        <div className="max-w-[980px] mx-auto px-6 text-center">
          <div className="text-sm font-semibold text-purple-600 mb-2">STORAGE</div>
          <h2 className="text-4xl md:text-5xl font-semibold mb-6 tracking-tight">
            Keep your things safe.<br />For as long as you need.
          </h2>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto mb-12 leading-relaxed">
            Climate-controlled facilities. 24/7 security. Flexible terms. 
            Your belongings are in good hands.
          </p>
          <div className="grid md:grid-cols-3 gap-8 max-w-3xl mx-auto">
            <div>
              <div className="text-3xl font-semibold mb-2">Climate</div>
              <div className="text-gray-600">controlled</div>
            </div>
            <div>
              <div className="text-3xl font-semibold mb-2">24/7</div>
              <div className="text-gray-600">Security</div>
            </div>
            <div>
              <div className="text-3xl font-semibold mb-2">Flexible</div>
              <div className="text-gray-600">Terms</div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-24">
        <div className="max-w-[980px] mx-auto px-6 text-center">
          <h2 className="text-4xl md:text-5xl font-semibold mb-6 tracking-tight">
            Ready to get moving?
          </h2>
          <p className="text-xl text-gray-600 mb-8">
            Get a free quote in 60 seconds.
          </p>
          <Button className="bg-blue-600 hover:bg-blue-700 h-12 px-6 rounded-full text-base">
            Get your free quote
          </Button>
          <div className="mt-4 text-sm text-gray-600">
            Or call us at (555) 123-4567
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer id="support" className="bg-gray-50 py-12 border-t border-gray-200">
        <div className="max-w-[980px] mx-auto px-6">
          <div className="grid grid-cols-2 md:grid-cols-5 gap-8 mb-8">
            <div>
              <div className="font-semibold mb-3 text-xs text-gray-500">Services</div>
              <ul className="space-y-2 text-xs">
                <li><a href="#" className="hover:underline">Residential</a></li>
                <li><a href="#" className="hover:underline">Commercial</a></li>
                <li><a href="#" className="hover:underline">Storage</a></li>
                <li><a href="#" className="hover:underline">Packing</a></li>
              </ul>
            </div>
            <div>
              <div className="font-semibold mb-3 text-xs text-gray-500">Company</div>
              <ul className="space-y-2 text-xs">
                <li><a href="#" className="hover:underline">About</a></li>
                <li><a href="#" className="hover:underline">Careers</a></li>
                <li><a href="#" className="hover:underline">Contact</a></li>
              </ul>
            </div>
            <div>
              <div className="font-semibold mb-3 text-xs text-gray-500">Support</div>
              <ul className="space-y-2 text-xs">
                <li><a href="#" className="hover:underline">FAQ</a></li>
                <li><a href="#" className="hover:underline">Get a Quote</a></li>
                <li><a href="#" className="hover:underline">Track Move</a></li>
              </ul>
            </div>
            <div>
              <div className="font-semibold mb-3 text-xs text-gray-500">Legal</div>
              <ul className="space-y-2 text-xs">
                <li><a href="#" className="hover:underline">Privacy</a></li>
                <li><a href="#" className="hover:underline">Terms</a></li>
                <li><a href="#" className="hover:underline">Insurance</a></li>
              </ul>
            </div>
            <div>
              <div className="font-semibold mb-3 text-xs text-gray-500">Contact</div>
              <ul className="space-y-2 text-xs">
                <li>hello@moowwee.com</li>
                <li>(555) 123-4567</li>
              </ul>
            </div>
          </div>
          <div className="border-t border-gray-200 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-600">
            <div>Copyright © 2026 moowwee Inc. All rights reserved.</div>
            <div className="flex gap-6">
              <a href="#" className="hover:underline">Privacy Policy</a>
              <a href="#" className="hover:underline">Terms of Use</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}

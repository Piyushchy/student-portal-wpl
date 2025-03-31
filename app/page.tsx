import { CalendarDays, Clock, FileText, GraduationCap, Menu, MessageSquare, User } from "lucide-react"
import Image from "next/image"
import Link from "next/link"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet"

export default function StudentPortal() {
  // Mock data - in a real app, this would come from an API or database
  const studentName = "Alex Johnson"
  const announcements = [
    {
      id: 1,
      title: "End of Semester Exams",
      content: "Final exams will begin on May 15th. Please check your timetable for details.",
      date: "2 days ago",
    },
    {
      id: 2,
      title: "Library Hours Extended",
      content: "The university library will remain open until midnight during exam week.",
      date: "1 week ago",
    },
    {
      id: 3,
      title: "Summer Registration Open",
      content: "Registration for summer courses is now open. Early registration ends April 30th.",
      date: "2 weeks ago",
    },
  ]

  const events = [
    { id: 1, title: "Math Assignment Due", date: "Today", time: "11:59 PM" },
    { id: 2, title: "Physics Lab", date: "Tomorrow", time: "2:00 PM" },
    { id: 3, title: "Study Group Meeting", date: "Apr 28", time: "4:30 PM" },
    { id: 4, title: "Career Fair", date: "May 2", time: "10:00 AM" },
  ]

  const courses = [
    { id: 1, code: "MATH101", name: "Calculus I", progress: 75 },
    { id: 2, code: "PHYS201", name: "Physics II", progress: 60 },
    { id: 3, code: "CS150", name: "Intro to Programming", progress: 90 },
    { id: 4, code: "ENG102", name: "Academic Writing", progress: 85 },
  ]

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      {/* Navigation - Mobile */}
      <div className="md:hidden flex items-center justify-between p-4 bg-white border-b">
        <div className="flex items-center gap-2">
          <Image
            src="/placeholder.svg?height=40&width=40"
            alt="University Logo"
            width={40}
            height={40}
            className="rounded"
          />
          <span className="font-semibold text-lg">Student Portal</span>
        </div>
        <Sheet>
          <SheetTrigger asChild>
            <Button variant="ghost" size="icon">
              <Menu className="h-6 w-6" />
              <span className="sr-only">Open menu</span>
            </Button>
          </SheetTrigger>
          <SheetContent side="right">
            <nav className="flex flex-col gap-4 mt-8">
              <Link href="#" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <GraduationCap className="h-5 w-5" />
                <span>Courses</span>
              </Link>
              <Link href="#" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <Clock className="h-5 w-5" />
                <span>Timetable</span>
              </Link>
              <Link href="#" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <User className="h-5 w-5" />
                <span>Attendance</span>
              </Link>
              <Link href="#" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <FileText className="h-5 w-5" />
                <span>Results</span>
              </Link>
              <Link href="#" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <MessageSquare className="h-5 w-5" />
                <span>Announcements</span>
              </Link>
            </nav>
          </SheetContent>
        </Sheet>
      </div>

      {/* Navigation - Desktop */}
      <div className="hidden md:flex items-center justify-between p-4 bg-white border-b">
        <div className="flex items-center gap-3">
          <Image
            src="/placeholder.svg?height=48&width=48"
            alt="University Logo"
            width={48}
            height={48}
            className="rounded"
          />
          <span className="font-semibold text-xl">Student Portal</span>
        </div>
        <nav className="flex items-center gap-6">
          <Link href="#" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <GraduationCap className="h-4 w-4" />
            <span>Courses</span>
          </Link>
          <Link href="#" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <Clock className="h-4 w-4" />
            <span>Timetable</span>
          </Link>
          <Link href="#" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <User className="h-4 w-4" />
            <span>Attendance</span>
          </Link>
          <Link href="#" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <FileText className="h-4 w-4" />
            <span>Results</span>
          </Link>
          <Link href="#" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <MessageSquare className="h-4 w-4" />
            <span>Announcements</span>
          </Link>
        </nav>
      </div>

      {/* Header */}
      <header className="bg-white p-6 md:p-10 border-b">
        <div className="max-w-7xl mx-auto">
          <h1 className="text-2xl md:text-3xl font-bold">Welcome, {studentName}!</h1>
          <p className="text-gray-500 mt-1">Spring Semester 2025 • Week 12</p>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 p-4 md:p-8">
        <div className="max-w-7xl mx-auto space-y-8">
          {/* Quick Access Buttons */}
          <section>
            <h2 className="text-lg font-semibold mb-4">Quick Access</h2>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <Button variant="outline" className="h-auto flex flex-col items-center justify-center p-4 gap-2">
                <Clock className="h-6 w-6" />
                <span>View Timetable</span>
              </Button>
              <Button variant="outline" className="h-auto flex flex-col items-center justify-center p-4 gap-2">
                <FileText className="h-6 w-6" />
                <span>Check Results</span>
              </Button>
              <Button variant="outline" className="h-auto flex flex-col items-center justify-center p-4 gap-2">
                <User className="h-6 w-6" />
                <span>Attendance Record</span>
              </Button>
              <Button variant="outline" className="h-auto flex flex-col items-center justify-center p-4 gap-2">
                <MessageSquare className="h-6 w-6" />
                <span>Contact Support</span>
              </Button>
            </div>
          </section>

          {/* Dashboard Grid */}
          <div className="grid md:grid-cols-3 gap-6">
            {/* Announcements Section */}
            <section className="md:col-span-2">
              <Card>
                <CardHeader>
                  <CardTitle>Important Announcements</CardTitle>
                  <CardDescription>Latest updates from your university</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {announcements.map((announcement) => (
                      <div key={announcement.id} className="border-b pb-4 last:border-0 last:pb-0">
                        <div className="flex justify-between items-start">
                          <h3 className="font-medium">{announcement.title}</h3>
                          <span className="text-xs text-gray-500">{announcement.date}</span>
                        </div>
                        <p className="text-sm text-gray-600 mt-1">{announcement.content}</p>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </section>

            {/* Calendar Overview */}
            <section>
              <Card>
                <CardHeader>
                  <CardTitle>Upcoming Events</CardTitle>
                  <CardDescription>Your schedule for the week</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-3">
                    {events.map((event) => (
                      <div key={event.id} className="flex items-start gap-3 border-b pb-3 last:border-0 last:pb-0">
                        <div className="bg-primary/10 rounded-md p-2 text-primary">
                          <CalendarDays className="h-5 w-5" />
                        </div>
                        <div>
                          <h4 className="font-medium text-sm">{event.title}</h4>
                          <p className="text-xs text-gray-500">
                            {event.date} • {event.time}
                          </p>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </section>
          </div>

          {/* Courses Overview */}
          <section>
            <h2 className="text-lg font-semibold mb-4">Your Courses</h2>
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
              {courses.map((course) => (
                <Card key={course.id}>
                  <CardHeader className="pb-2">
                    <CardTitle className="text-base">{course.name}</CardTitle>
                    <CardDescription>{course.code}</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-2">
                      <div className="flex justify-between text-sm">
                        <span>Progress</span>
                        <span className="font-medium">{course.progress}%</span>
                      </div>
                      <div className="w-full bg-gray-200 rounded-full h-2">
                        <div className="bg-primary h-2 rounded-full" style={{ width: `${course.progress}%` }}></div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-white border-t p-6 md:p-8">
        <div className="max-w-7xl mx-auto">
          <div className="grid md:grid-cols-3 gap-8">
            <div>
              <h3 className="font-semibold mb-3">Contact Information</h3>
              <address className="not-italic text-sm text-gray-600 space-y-1">
                <p>University Student Services</p>
                <p>123 Campus Drive</p>
                <p>Email: support@university.edu</p>
                <p>Phone: (555) 123-4567</p>
              </address>
            </div>
            <div>
              <h3 className="font-semibold mb-3">Quick Links</h3>
              <ul className="text-sm space-y-2">
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Academic Calendar
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Student Handbook
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Library Resources
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    IT Support
                  </Link>
                </li>
              </ul>
            </div>
            <div>
              <h3 className="font-semibold mb-3">Connect With Us</h3>
              <div className="flex gap-4">
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                  </svg>
                  <span className="sr-only">Facebook</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                  </svg>
                  <span className="sr-only">Instagram</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                  </svg>
                  <span className="sr-only">Twitter</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                  </svg>
                  <span className="sr-only">LinkedIn</span>
                </Link>
              </div>
              <div className="mt-4 text-sm text-gray-600">
                <p>© 2025 University Name. All rights reserved.</p>
                <div className="mt-2 flex gap-4">
                  <Link href="#" className="hover:text-primary">
                    Privacy Policy
                  </Link>
                  <Link href="#" className="hover:text-primary">
                    Terms of Use
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}

